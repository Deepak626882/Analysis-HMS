select
    SUM(paycharge.amtdr) - SUM(paycharge.amtcr) AS DebitAmt,
    MAX(paycharge.comments) as Comments,
    paycharge.paycode,
    MAX(revmast.ac_code) AS ACCode,
    MAX(depart.name) as Department,
    MAX(revmast.name) as RevenueName,
    MAX(paycharge.vdate) as vdate,
    MAX(paycharge.vprefix) as vprefix
from
    paycharge
    left join revmast on paycharge.paycode = revmast.rev_code
    left join depart on paycharge.restcode = depart.dcode
where
    not paycharge.roomtype = 'RO'
    and paycharge.vdate between '2025-07-10'
    and '2025-07-10'
    and paycharge.propertyid = 117
    and not paycharge.restcode = 'FOM117'
group by
    paycharge.paycode,
    paycharge.restcode

    -- Second
    -- 
select
    (paycharge.amtcr - paycharge.amtdr) as CreditAmt,
    paycharge.paycode,
    revmast.ac_code,
    revmast.name as RevenueName,
    paycharge.comments,
    paycharge.vdate,
    paycharge.vprefix
from
    paycharge
    left join revmast on revmast.rev_code = paycharge.paycode
where
    paycharge.propertyid = 117
    and paycharge.vdate between '2025-07-10'
    and '2025-07-10'
    and paycharge.vtype in ('ARRES', 'ADRES')
    and paycharge.docid not in (
        select
            distinct contraid
        from
            paycharge
        where
            contraid is not null
            and contraid <> ''
    )
    and paycharge.paytype = 'Cash'
    and paycharge.restcode = 'FOM117'


    