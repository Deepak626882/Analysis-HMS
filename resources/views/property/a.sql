SELECT
    MAX(SACCode) AS SACCode,
    MAX(Department) AS HeadName,
    CONCAT(MAX(VType), '/24-25/', MIN(BillNo)) AS SrlFrom,
    CONCAT(MAX(VType), '/24-25/', MAX(BillNo)) AS SrlTo,
    (MAX(BillNo) - MIN(BillNo) + 1) AS BillCount,
    (
        MAX(BillNo) - MIN(BillNo) - COUNT(DISTINCT DocId) + 1
    ) AS Cancelled,
    ROUND(
        SUM(BaseValue) + SUM(IGST) + SUM(CGST) + SUM(SGST),
        0
    ) AS Total,
    ROUND(SUM(BaseValue), 2) AS Taxable,
    ROUND(SUM(IGST), 2) AS IGST,
    ROUND(SUM(CGST), 2) AS CGST,
    ROUND(SUM(SGST), 2) AS SGST,
    ROUND(SUM(TaxAmtOther), 2) AS CESS
FROM
    (
        SELECT
            S1.docId,
            S1.restcode,
            '996334' AS SACCode,
            CASE
                WHEN S1.vtype = 'IDC' THEN 'INDOOR BANQUET'
                ELSE 'OUTDOOR BANQUET'
            END AS Department,
            S1.vtype,
            S1.vdate AS Bill_Date,
            S1.vno AS BillNo,
            S1.party AS GuestName,
            S1.netamt AS BillTotal,
            Q.IGST,
            Q.CGST,
            Q.SGST,
            Q.BASEVALUE,
            Q.TAXPER,
            IFNULL(Q.TaxAmtOther, 0) AS TaxAmtOther,
            TRIM(IFNULL(SG.GSTIN, '')) AS GSTIN,
            IFNULL(HB.CompanyCode, '') AS Company,
            CASE
                WHEN (
                    IFNULL(Q.BASEVALUE, 0) <> 0
                    OR IFNULL(Q.TaxAmtOther, 0) <> 0
                ) THEN 1
                ELSE 0
            END AS Cond1
        FROM
            (
                SELECT
                    T1.docId,
                    SUM(T1.IGST) AS IGST,
                    SUM(T1.CGST) AS CGST,
                    SUM(T1.SGST) AS SGST,
                    SUM(T1.BASEVALUE) AS BASEVALUE,
                    MAX(T1.TAXPER) AS TAXPER,
                    SUM(T1.TaxAmtOther) AS TaxAmtOther,
                    SUM(T1.BaseValueOther) AS BaseValueOther,
                    SUM(T1.TaxPerOther) AS TaxPerOther
                FROM
                    (
                        SELECT
                            SL.docId,
                            MAX(SL.sno) AS SNo,
                            IFNULL(
                                SUM(
                                    CASE
                                        WHEN SL.nature IN ('IGST') THEN SL.taxamt
                                    END
                                ),
                                0
                            ) AS IGST,
                            IFNULL(
                                SUM(
                                    CASE
                                        WHEN SL.nature IN ('CGST') THEN SL.taxamt
                                    END
                                ),
                                0
                            ) AS CGST,
                            IFNULL(
                                SUM(
                                    CASE
                                        WHEN SL.nature IN ('SGST') THEN SL.taxamt
                                    END
                                ),
                                0
                            ) AS SGST,
                            IFNULL(
                                MAX(
                                    CASE
                                        WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.basevalue
                                    END
                                ),
                                0
                            ) AS BASEVALUE,
                            IFNULL(
                                SUM(
                                    CASE
                                        WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.taxper
                                    END
                                ),
                                0
                            ) AS TAXPER,
                            IFNULL(
                                SUM(
                                    CASE
                                        WHEN SL.nature IN ('Luxury Tax', 'Sale Tax', 'Service Tax') THEN SL.taxamt
                                    END
                                ),
                                0
                            ) AS TaxAmtOther,
                            IFNULL(
                                MAX(
                                    CASE
                                        WHEN SL.nature IN ('Luxury Tax', 'Sale Tax', 'Service Tax') THEN SL.basevalue
                                    END
                                ),
                                0
                            ) AS BaseValueOther,
                            IFNULL(
                                SUM(
                                    CASE
                                        WHEN SL.nature IN ('Luxury Tax', 'Sale Tax', 'Service Tax') THEN SL.taxper
                                    END
                                ),
                                0
                            ) AS TaxPerOther
                        FROM
                            (
                                SELECT
                                    T.docId,
                                    MAX(T.sno) AS SNo,
                                    SM.nature,
                                    T.taxcode,
                                    T.taxper,
                                    SUM(T.taxamt) AS TaxAmt,
                                    SUM(T.basevalue) AS BaseValue
                                FROM
                                    (
                                        SELECT
                                            DocId,
                                            SNo,
                                            Taxcode,
                                            TaxPer,
                                            TaxAmt,
                                            BaseValue
                                        FROM
                                            hallsale2
                                        WHERE
                                            vdate BETWEEN '2025-04-14'
                                            AND '2025-04-14'
                                        UNION
                                        ALL
                                        SELECT
                                            docid,
                                            0 AS SNo,
                                            revcode,
                                            svalue,
                                            (BaseAmount * SValue / 100),
                                            BaseAmount
                                        FROM
                                            suntranh
                                        WHERE
                                            vdate BETWEEN '2025-04-14'
                                            AND '2025-04-14'
                                            AND revcode <> ''
                                            AND svalue > 0
                                            AND amount > 0
                                    ) T
                                    INNER JOIN revmast ON revmast.rev_code = T.taxcode
                                    LEFT JOIN sundrymast SM ON revmast.sundry = SM.sundry_code
                                WHERE
                                    revmast.flag_type = 'T'
                                GROUP BY
                                    T.docId,
                                    SM.nature,
                                    T.taxcode,
                                    T.taxper
                            ) SL
                        GROUP BY
                            SL.docId,
                            SL.taxper
                    ) T1
                GROUP BY
                    T1.docId,
                    T1.SNo
            ) Q
            INNER JOIN hallsale1 S1 ON S1.docId = Q.docId
            INNER JOIN hallbook HB ON HB.docid = S1.bookdocid
            LEFT JOIN subgroup SG ON SG.sub_code = HB.companycode
    ) H
GROUP BY
    H.restcode;