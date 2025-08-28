SELECT
    Q.DocId,
    Q.AmtCr,
    Q.IGST,
    Q.CGST,
    Q.SGST,
    Q.BASEVALUE,
    Q.TAXPER,
    Q.TaxAmtOther,
    Q.TaxPerOther,
    Q.BaseValueOther,
    PH.FPNo,
    PH.ContraDocID,
    PH.BillDocId,
    Q.AdDate,
    PH.Vdate,
    CASE
        WHEN Q.AdDate BETWEEN '2025-04-14' AND '2025-04-14' AND PH.vdate IS NULL THEN 1
        WHEN Q.AdDate BETWEEN '2025-04-14' AND '2025-04-14'
             AND (PH.Vdate NOT BETWEEN '2025-04-14' AND '2025-04-14')
             AND DATE_FORMAT(Q.AdDate, '%Y-%m') < DATE_FORMAT(PH.Vdate, '%Y-%m')
             AND IFNULL(BillDocId, '') <> '' THEN 1
        WHEN Q.AdDate BETWEEN '2025-04-14' AND '2025-04-14'
             AND PH.Vdate BETWEEN '2025-04-14' AND '2025-04-14'
             AND DATE_FORMAT(Q.AdDate, '%Y-%m') < DATE_FORMAT(PH.Vdate, '%Y-%m')
             AND IFNULL(BillDocId, '') <> '' THEN 3
        WHEN DATE_FORMAT(Q.AdDate, '%Y-%m') <> DATE_FORMAT(PH.vdate, '%Y-%m')
             AND IFNULL(BillDocId, '') <> '' THEN 2
        ELSE 0
    END AS cond1
FROM (
    SELECT
        SL.docid,
        MAX(SL.vdate) AS AdDate,
        IFNULL(SUM(SL.amtcr), 0) AS AmtCr,
        IFNULL(SUM(CASE WHEN SL.nature = 'IGST' THEN SL.amtcr ELSE 0 END), 0) AS IGST,
        IFNULL(SUM(CASE WHEN SL.nature = 'CGST' THEN SL.amtcr ELSE 0 END), 0) AS CGST,
        IFNULL(SUM(CASE WHEN SL.nature = 'SGST' THEN SL.amtcr ELSE 0 END), 0) AS SGST,
        IFNULL(MAX(CASE WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.basevalue END), 0) AS BASEVALUE,
        IFNULL(SUM(CASE WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.taxper END), 0) AS TAXPER,
        0 AS TaxAmtOther,
        0 AS TaxPerOther,
        0 AS BaseValueOther
    FROM (
        SELECT
            P.docid,
            SM.nature,
            P.paycode,
            P.vdate,
            P.amtcr,
            P.taxper,
            onamt AS basevalue
        FROM paychargeh AS P
        LEFT JOIN revmast ON P.paycode = revmast.rev_code
        LEFT JOIN sundrymast SM ON revmast.sundry = SM.sundry_code
        WHERE SM.nature IN ('CGST', 'SGST', 'IGST')
            AND P.amtcr <> 0
            AND P.vtype = 'AD'
    ) SL
    GROUP BY SL.docid, SL.taxper
) Q
INNER JOIN (
    SELECT
        paychargeh.docid,
        paychargeh.fpno,
        paychargeh.contradocid,
        IFNULL(HS1.docid, '') AS BillDocId,
        HS1.vdate
    FROM paychargeh
    LEFT JOIN hallsale1 HS1 ON paychargeh.contradocid = HS1.bookdocid
    WHERE IFNULL(paychargeh.contradocid, '') <> ''
        AND paychargeh.vtype = 'AD'
) PH ON Q.docid = PH.docid
WHERE
    (Q.AdDate BETWEEN '2025-04-14' AND '2025-04-14'
    OR PH.vdate BETWEEN '2025-04-14' AND '2025-04-14')
ORDER BY cond1;
