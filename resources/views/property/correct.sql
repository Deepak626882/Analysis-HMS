SELECT
    S1.docid FolioNoDocId,
    S1.vdate Bill_Date,
    '' + RTrim(LTrim(S1.vtype)) + '/' + '24-25/' + Cast(S1.vno As Varchar) BillNo,
    IsNull(SG.conperson, '') GuestName,
    S1.netamt BillTotal,
    Q.TAXAMT,
    Q.BASEVALUE,
    Q.TAXPER,
    IsNull(Q.TaxAmtOther, 0) TaxAmtOther,
    IsNull(Q.Exempted, 0) Exempted,
    IsNull(Q.NonGST, 0) NonGST,
    0 NilRated,
    LTRIM(RTRIM(IsNull(SG.gstin, ''))) GSTIN,
    LTRIM(RTRIM(IsNull(SG.name, ''))) CompanyName,
    IsNull(S1.party, '') Company,
    '' EGSTIN,
Case
        When (
            IsNull(Q.BASEVALUE, 0) <> 0
            OR IsNull(Q.TaxAmtOther, 0) <> 0
        ) Then 1
        Else 0
    End Cond1,
Case
        When (
            IsNull(Q.Exempted, 0) <> 0
            OR IsNull(Q.NonGST, 0) <> 0
        ) Then 1
        Else 0
    End Cond2
FROM
    (
        Select
            T1.DocId,
            Sum(T1.TAXAMT) TAXAMT,
            Sum(T1.BASEVALUE) BASEVALUE,
            Max(T1.TAXPER) TAXPER,
            Sum(T1.TaxAmtOther) TaxAmtOther,
            Sum(T1.BaseValueOther) BaseValueOther,
            Sum(T1.TaxPerOther) TaxPerOther,
            Sum(T1.Exempted) Exempted,
            Sum(T1.NonGST) NonGST
        From
            (
                SELECT
                    SL.DocId,
                    Max(SL.SNo) SNo,
                    IsNull(
                        Sum(
                            Case
                                When SL.Nature In ('CGST', 'SGST', 'IGST') Then SL.TaxAmt
                            End
                        ),
                        0
                    ) As TAXAMT,
                    IsNull(
                        MAX(
                            Case
                                When SL.Nature In ('CGST', 'SGST', 'IGST') Then SL.BaseValue
                            End
                        ),
                        0
                    ) As BASEVALUE,
                    IsNull(
                        SUM(
                            Case
                                When SL.Nature In ('CGST', 'SGST', 'IGST') Then SL.TaxPer
                            End
                        ),
                        0
                    ) As TAXPER,
                    IsNull(
                        Sum(
                            Case
                                When SL.Nature In ('Luxury Tax', 'Sale Tax', 'Service Tax') Then SL.TaxAmt
                            End
                        ),
                        0
                    ) As TaxAmtOther,
                    IsNull(
                        MAX(
                            Case
                                When SL.Nature In ('Luxury Tax', 'Sale Tax', 'Service Tax') Then SL.BaseValue
                            End
                        ),
                        0
                    ) As BaseValueOther,
                    IsNull(
                        SUM(
                            Case
                                When SL.Nature In ('Luxury Tax', 'Sale Tax', 'Service Tax') Then SL.TaxPer
                            End
                        ),
                        0
                    ) As TaxPerOther,
                    0 Exempted,
                    0 NonGST
                From
                    (
                        Select
                            T.DocId,
                            Max(T.SNo) SNo,
                            SM.Nature,
                            T.Taxcode,
                            T.TaxPer,
                            sum(T.TaxAmt) as TaxAmt,
                            sum(T.BaseValue) as BaseValue
                        From
                            (
                                Select
                                    DocId,
                                    SNo,
                                    Taxcode,
                                    TaxPer,
                                    TaxAmt,
                                    BaseValue
                                from
                                    Sale2
                                    Inner Join Depart On Depart.Code = Sale2.RestCode
                                Where
                                    DelFlag = 'N'
                                    And VDate Between '20/Mar/2025'
                                    And '31/Mar/2025'
                                    And Depart.RestType In ('Outlet', 'Room Service')
                                Union
                                All
                                select
                                    DocId,
                                    0 As SNo,
                                    RevCode,
                                    Svalue,
(BaseAmount * SValue / 100),
                                    BaseAmount
                                from
                                    SunTran
                                    Inner Join Depart On Depart.Code = SunTran.RestCode
                                Where
                                    DelFlag = 'N'
                                    And VDate Between '20/Mar/2025'
                                    And '31/Mar/2025'
                                    And Depart.RestType In ('Outlet', 'Room Service')
                                    And RevCode <> ''
                                    And SValue > 0
                                    And Amount > 0
                            ) T
                            Inner Join RevMast on RevMast.code = T.Taxcode
                            Left Join SundryMast SM ON RevMast.Sundry = SM.Code
                        Where
                            RevMast.FieldType = 'T'
                        Group by
                            T.DocId,
                            SM.Nature,
                            T.Taxcode,
                            T.TaxPer
                    ) SL
                Group By
                    SL.DocId,
                    SL.TaxPer
            ) T1
        Group By
            T1.DocId,
            T1.SNo
        Union
        All
        Select
            DocId,
            0 TaxAmt,
            0 BaseValue,
            0 TaxPer,
            0 TaxAmtOther,
            0 BaseValueOther,
            0 TaxPerOther,
            Sum(Amount - DiscAmt) Exempted,
            0 NonGST
        From
            Stock
            Inner Join Itemmast On Itemmast.Code = Stock.Item
            And Itemmast.RestCode = Stock.ItemRestCode
            Inner Join ItemCatMast On ItemCatMast.Code = Itemmast.ItemCatCode
            Inner Join Depart On Depart.Code = Stock.RestCode
        Where
            DelFlag = 'N'
            And VDate Between '20/Mar/2025'
            And '31/Mar/2025'
            And Depart.RestType In ('Outlet', 'Room Service')
            And TaxPer = 0
            And TaxAmt = 0
            And ItemCatMast.CatType <> 'Liquor'
        Group By
            DocId
        Union
        All
        Select
            DocId,
            0 TaxAmt,
            0 BaseValue,
            0 TaxPer,
            0 TaxAmtOther,
            0 BaseValueOther,
            0 TaxPerOther,
            0 Exempted,
            Sum(Amount - DiscAmt) NonGST
        From
            Stock
            Inner Join Itemmast On Itemmast.Code = Stock.Item
            And Itemmast.RestCode = Stock.ItemRestCode
            Inner Join ItemCatMast On ItemCatMast.Code = Itemmast.ItemCatCode
            Inner Join Depart On Depart.Code = Stock.RestCode
        Where
            DelFlag = 'N'
            And VDate Between '20/Mar/2025'
            And '31/Mar/2025'
            And Depart.RestType In ('Outlet', 'Room Service')
            And TaxPer = 0
            And TaxAmt = 0
            And ItemCatMast.CatType = 'Liquor'
        Group By
            DocId
    ) Q
    INNER JOIN Sale1 S1 ON S1.docid = Q.DocId
    LEFT JOIN SubGroup SG ON SG.SubCode = S1.Party
ORDER BY
    S1.vtype,
    S1.vno,
    Q.TAXPER Desc
Select
    T.*,
    0 Exempted,
    0 NonGST,
    S.BillTotal,
    '' + 'BCNT/' + '24-25/' + T.Bill_No As BillNo,
    LTRIM(RTRIM(IsNull(SG.GSTIN, ''))) GSTIN,
    LTRIM(RTRIM(IsNull(SG.Name, ''))) CompanyName,
    IsNull(GF.Company, '') Company,
    LTRIM(RTRIM(IsNull(TA.GSTIN, ''))) EGSTIN,
    IsNull(GF.TravelAgent, '') TravelAgent,
Case
        When (
            T.BaseValue <> 0
            OR T.TaxAmtOther <> 0
        ) Then 1
        Else 0
    End Cond1,
Case
        When (T.NilRated <> 0) Then 1
        Else 0
    End Cond2
From
    (
        Select
            R.FolionoDocid,
            R.Foliono,
            R.Bill_Date,
            R.Bill_No,
            R.AmtDr,
            R.BASEVALUE,
            R.TAXPER,
            0 TaxAmtOther,
            0 TaxPerOther,
            0 BaseValueOther,
            0 NilRated
        From
            (
                SELECT
                    Max(Q.FolionoDocid) FolionoDocid,
                    Max(Q.Foliono) Foliono,
                    Max(Q.SettleDate) Bill_Date,
                    Max(Q.Bill_No) Bill_No,
                    Sum(Q.AmtDr) AmtDr,
                    Sum(Q.BASEVALUE) BASEVALUE,
                    Max(Q.TAXPER) TAXPER
                FROM
                    (
                        SELECT
                            SL.DocId,
                            SL.FolionoDocid,
                            SL.FolioNo,
                            SL.SettleDate,
                            MAX(SL.Bill_No) AS BILL_NO,
                            IsNull(Sum(SL.AmtDr), 0) As AmtDr,
                            IsNull(Max(SL.BASEVALUE), 0) As BASEVALUE,
                            IsNull(SUM(SL.TAXPER), 0) As TAXPER
                        From
                            (
                                SELECT
                                    P.FolioNoDocId,
                                    P.DocId,
                                    P.PayCode,
                                    P.Vdate,
                                    P.FolioNo,
                                    P.SettleDate,
                                    P.Bill_No,
                                    P.AmtDr - P.AmtCr as AmtDr,
                                    P.TaxPer,
CASE
                                        WHEN (P.AmtDr - P.AmtCr) > 0 THEN P.OnAmt
                                        ELSE - P.OnAmt
                                    END BaseValue
                                FROM
                                    PayCharge AS P
                                    Left JOIN RevMast ON P.PayCode = RevMast.Code
                                    LEFT JOIN SundryMast ON RevMast.Sundry = SundryMast.Code
                                Where
                                    SundryMast.Nature In ('CGST', 'SGST', 'IGST')
                                    And P.RoomType = 'RO'
                                    And P.SettleDate Between '20/Mar/2025'
                                    And '31/Mar/2025'
                                    AND AmtDr - Amtcr <> 0
                                    AND P.FOliono <> 0
                                    AND SettleDate is Not NULL
                                    AND IsNull(Bill_No, '') <> ''
                            ) SL
                        Group By
                            SL.DocId,
                            SL.FolioNoDocId,
                            SL.FolioNo,
                            SL.SettleDate,
                            SL.TaxPer
                    ) Q
                GROUP BY
                    FOLIONODOCID,
                    TAXPER
            ) R
        Union
        All
        Select
            P.FolionoDocId,
            Max(P.FolioNo) FolioNo,
            Max(P.SettleDate) Bill_Date,
            Max(P.Bill_No) Bill_No,
            0 AmtDr,
            0 BASEVALUE,
            0 TAXPER,
            Sum(P.AmtDr - P.AmtCr) TaxAmtOther,
            Sum(P.TaxPer) TaxPerOther,
            Sum(
                CASE
                    WHEN (P.AmtDr - P.AmtCr) > 0 THEN P.OnAmt
                    ELSE - P.OnAmt
                END
            ) BaseValueOther,
            0 NilRated
        FROM
            PayCharge AS P
            Left JOIN RevMast ON P.PayCode = RevMast.Code
            LEFT JOIN SundryMast ON RevMast.Sundry = SundryMast.Code
        Where
            SundryMast.Nature In ('Luxury Tax', 'Sale Tax', 'Service Tax')
            And P.RoomType = 'RO'
            And P.SettleDate Between '20/Mar/2025'
            And '31/Mar/2025'
            AND AmtDr - Amtcr <> 0
            AND P.FOliono <> 0
            AND SettleDate is Not NULL
            AND IsNull(Bill_No, '') <> ''
        Group By
            FolioNoDocid
        Union
        All
        Select
            N.FolionoDocId,
            Max(N.FolioNo) FolioNo,
            Max(N.SettleDate) Bill_Date,
            Max(N.Bill_No) Bill_No,
            0 AmtDr,
            0 BASEVALUE,
            0 TAXPER,
            0 TaxAmtOther,
            0 TaxPerOther,
            0 BaseValueOther,
            Sum(NilAmt) NilRated
        From
            (
                Select
                    FolioNoDocId,
                    DocId,
                    Max(FolioNo) FolioNo,
                    Max(SettleDate) SettleDate,
                    Max(Bill_No) Bill_No,
                    Sum(AmtDr - AmtCr) NilAmt
                From
                    paycharge
                where
                    docid In (
                        Select
                            docid
                        From
                            paycharge
                        Where
                            paycode In (
                                Select
                                    code
                                FROM
                                    RevMast
                                WHERE
                                    code Not In ('KKROFF', 'KKTOUT')
                                    And FlagAMR = 'Detail'
                                    and FLAGTYPE = 'FOM'
                            )
                            And SettleDate Between '20/Mar/2025'
                            And '31/Mar/2025'
                            And RoomType = 'RO'
                            And FolioNo <> 0
                    )
                Group By
                    FolionoDocId,
                    DocId
                Having
                    Sum(TaxPer) = 0
            ) N
        Group By
            Folionodocid
    ) T
    INNER JOIN (
        Select
            FolioNoDocId,
            Sum(AmtDr - AmtCr) BillTotal
        from
            PayCharge
        where
            (
                DocId In (
                    Select
                        DocId
                    From
                        Paycharge
                    Where
                        PayCode = 'KKDISC'
                )
                Or (
                    AmtDr <> 0
                    And IsNull(Modeset, '') <> 'S'
                )
                Or (
                    Modeset = 'S'
                    and PayCode = 'KKROFF'
                )
            )
            And RoomType = 'RO'
            And SettleDate Between '20/Mar/2025'
            And '31/Mar/2025'
            And FolioNo <> 0
        Group By
            FolioNoDocid
    ) S ON T.FolionoDocid = S.FolionoDocId
    INNER JOIN GuestFolio GF ON T.FolionoDocid = GF.DocId
    LEFT JOIN SubGroup SG On GF.Company = SG.SubCode
    LEFT JOIN SubGroup TA On GF.TravelAgent = TA.SubCode
ORDER BY
    Convert(FLOAT, Bill_NO),
    T.FOLIONODOCID,
    T.TAXPER Desc