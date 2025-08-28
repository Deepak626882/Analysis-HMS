select
    P.sno1,
    P.sno,
    P.FolioNoDocId,
    P.DocId,
    SM.Nature,
    P.PayCode,
    '996311' as hsncode,
    P.Vdate,
    P.FolioNo,
    P.SettleDate,
    P.billno,
    SUM(P.amtdr) as taxsum,
    SUM(P.billamount) as taxableamount,
    P.taxper,
    CASE
        WHEN (P.AmtDr - P.AmtCr) > 0 THEN P.OnAmt
        ELSE - P.OnAmt
    END as BaseValue,
    subgroup.name as company,
    subgroup.gstin,
    SUM(
        CASE
            WHEN SM.nature = 'CGST' THEN P.amtdr
            ELSE 0
        END
    ) cgst,
    SUM(
        CASE
            WHEN SM.nature = 'SGST' THEN P.amtdr
            ELSE 0
        END
    ) sgst,
    SM.nature as Nature,
    SUM(P.amtdr) + SUM(P.billamount) + SUM(P.amtdr) as netamount
from
    paycharge as P
    left join revmast on P.PayCode = revmast.rev_code
    and revmast.propertyid = 119
    left join sundrymast as SM on revmast.Sundry = SM.sundry_code
    and SM.propertyid = 119
    left join guestfolio on guestfolio.docid = P.folionodocid
    and guestfolio.propertyid = 119
    left join subgroup on subgroup.sub_code = guestfolio.company
    and subgroup.propertyid = 119
where
    SM.Nature in ('CGST', 'SGST', 'IGST')
    and P.SettleDate between '2025-07-15'
    and '2025-07-25'
    and P.FolioNo <> 0
    and P.SettleDate is not null
    and P.billno <> 0
    and P.propertyid = 119
group by
    revmast.hsn_code,
    P.taxper,
    guestfolio.company,
    P.paycode