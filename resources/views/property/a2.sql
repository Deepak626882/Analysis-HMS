SELECT
    S1.docId AS FolioNoDocId,
    S1.vdate AS Bill_Date,
    S1.vtype as vtype,
    S1.VNo AS BillNo,
    S1.party AS GuestName,
    S1.netamt AS BillTotal,
    Q.TAXAMT,
    Q.BASEVALUE,
    Q.TAXPER,
    IFNULL(Q.TaxAmtOther, 0) AS TaxAmtOther,
    IFNULL(Q.Exempted, 0) AS Exempted,
    IFNULL(Q.NonGST, 0) AS NonGST,
    0 AS NilRated,
    TRIM(IFNULL(SG.gstin, '')) AS GSTIN,
    TRIM(IFNULL(SG.name, '')) AS CompanyName,
    IFNULL(HB.companycode, '') AS Company,
    TRIM(IFNULL(BA.GSTIN, '')) AS EGSTIN,
    IFNULL(HB.bookingagent, '') AS TravelAgent,
    CASE
        WHEN (
            IFNULL(Q.BASEVALUE, 0) <> 0
            OR IFNULL(Q.TaxAmtOther, 0) <> 0
        ) THEN 1
        ELSE 0
    END AS Cond1,
    CASE
        WHEN (
            IFNULL(Q.Exempted, 0) <> 0
            OR IFNULL(Q.NonGST, 0) <> 0
        ) THEN 1
        ELSE 0
    END AS Cond2
FROM
    (
        SELECT
            T1.DocId,
            SUM(T1.TAXAMT) AS TAXAMT,
            SUM(T1.BASEVALUE) AS BASEVALUE,
            MAX(T1.TAXPER) AS TAXPER,
            SUM(T1.TaxAmtOther) AS TaxAmtOther,
            SUM(T1.BaseValueOther) AS BaseValueOther,
            SUM(T1.TaxPerOther) AS TaxPerOther,
            SUM(T1.Exempted) AS Exempted,
            SUM(T1.NonGST) AS NonGST
        FROM
            (
                SELECT
                    SL.docId,
                    MAX(SL.sno) AS SNo,
                    IFNULL(
                        SUM(
                            CASE
                                WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.TaxAmt
                            END
                        ),
                        0
                    ) AS TAXAMT,
                    IFNULL(
                        MAX(
                            CASE
                                WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.BaseValue
                            END
                        ),
                        0
                    ) AS BASEVALUE,
                    IFNULL(
                        SUM(
                            CASE
                                WHEN SL.nature IN ('CGST', 'SGST', 'IGST') THEN SL.TaxPer
                            END
                        ),
                        0
                    ) AS TAXPER,
                    IFNULL(
                        SUM(
                            CASE
                                WHEN SL.nature IN ('Luxury Tax', 'Sale Tax', 'Service Tax') THEN SL.TaxAmt
                            END
                        ),
                        0
                    ) AS TaxAmtOther,
                    IFNULL(
                        MAX(
                            CASE
                                WHEN SL.nature IN ('Luxury Tax', 'Sale Tax', 'Service Tax') THEN SL.BaseValue
                            END
                        ),
                        0
                    ) AS BaseValueOther,
                    IFNULL(
                        SUM(
                            CASE
                                WHEN SL.nature IN ('Luxury Tax', 'Sale Tax', 'Service Tax') THEN SL.TaxPer
                            END
                        ),
                        0
                    ) AS TaxPerOther,
                    0 AS Exempted,
                    0 AS NonGST
                FROM
                    (
                        SELECT
                            T.DocId,
                            MAX(T.SNo) AS SNo,
                            SM.Nature,
                            T.Taxcode,
                            T.TaxPer,
                            SUM(T.TaxAmt) AS TaxAmt,
                            SUM(T.BaseValue) AS BaseValue
                        FROM
                            (
                                SELECT
                                    docid,
                                    sno,
                                    taxcode,
                                    taxper,
                                    taxamt,
                                    basevalue
                                FROM
                                    hallsale2
                                WHERE
                                    vdate BETWEEN '2025-04-14'
                                    AND '2025-04-14'
                                    AND hallsale2.propertyid = 103
                                UNION
                                ALL
                                SELECT
                                    DocId,
                                    0 AS SNo,
                                    RevCode,
                                    Svalue,
                                    (BaseAmount * SValue / 100),
                                    BaseAmount
                                FROM
                                    suntranh
                                WHERE
                                    VDate BETWEEN '2025-04-14'
                                    AND '2025-04-14'
                                    AND suntranh.propertyid = 103
                                    AND revcode <> ''
                                    AND SValue > 0
                                    AND Amount > 0
                            ) T
                            INNER JOIN revmast ON revmast.rev_code = T.taxcode
                            LEFT JOIN sundrymast SM ON revmast.sundry = SM.sundry_code
                        WHERE
                            revmast.field_type = 'T'
                        GROUP BY
                            T.DocId,
                            SM.nature,
                            T.taxcode,
                            T.taxper
                    ) SL
                GROUP BY
                    SL.DocId,
                    SL.taxper
            ) T1
        GROUP BY
            T1.docId,
            T1.sno
        UNION
        ALL
        SELECT
            DocId,
            0,
            0,
            0,
            0,
            0,
            0,
            SUM(Amount - DiscAmt),
            0
        FROM
            hallstock
            INNER JOIN itemmast ON itemmast.Code = hallstock.item
            AND itemmast.RestCode = hallstock.restcode
            INNER JOIN itemcatmast ON itemcatmast.Code = itemmast.ItemCatCode
            INNER JOIN depart ON depart.dcode = hallstock.restcode
        WHERE
            VDate BETWEEN '2025-04-14'
            AND '2025-04-14'
            AND hallstock.propertyid = 103
            AND taxper = 0
            AND taxamt = 0
            AND itemcatmast.CatType <> 'Liquor'
        GROUP BY
            DocId
        UNION
        ALL
        SELECT
            DocId,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            SUM(Amount - DiscAmt)
        FROM
            hallstock
            INNER JOIN itemmast ON itemmast.Code = hallstock.item
            AND itemmast.RestCode = hallstock.restcode
            INNER JOIN itemcatmast ON itemcatmast.Code = itemmast.ItemCatCode
            INNER JOIN depart ON depart.dcode = hallstock.restcode
        WHERE
            VDate = '2025-04-14'
            AND hallstock.propertyid = 103
            AND taxper = 0
            AND taxamt = 0
            AND itemcatmast.CatType = 'Liquor'
        GROUP BY
            DocId
    ) Q
    INNER JOIN hallsale1 S1 ON S1.docId = Q.docid
    INNER JOIN hallbook HB ON HB.docid = S1.bookdocid
    LEFT JOIN subgroup SG ON SG.sub_code = HB.companycode
    LEFT JOIN subgroup BA ON HB.bookingagent = BA.sub_code
ORDER BY
    S1.vtype,
    S1.vno,
    Q.taxper DESC;