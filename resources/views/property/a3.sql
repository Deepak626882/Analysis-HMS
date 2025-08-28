select
    S.vdate,
    S.vtype,
    S.vno,
    S.amount,
    S.item,
    I.Name,
    CASE
        WHEN VT.ncat IN (
            'PBC',
            'PBR',
            'MRE',
            'RQI',
            'STOP',
            'BKREC',
            'KSREC',
            'KMREC'
        ) THEN S.recdqty
        ELSE 0
    END as QtyRec,
    CASE
        WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') THEN S.issqty
        ELSE 0
    END as QtyIss,
    CASE
        WHEN VT.ncat IN ('PBC', 'PBR', 'PRR', 'PRC', 'MRE') THEN SG.name
        ELSE D.name
    END as Particulars,
    CASE
        WHEN VT.ncat IN (
            'PBC',
            'PBR',
            'MRE',
            'RQI',
            'STOP',
            'BKREC',
            'KSREC',
            'KMREC'
        ) THEN 'A'
        WHEN VT.ncat IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS') THEN 'B'
        ELSE 'C'
    END as SeqNo
from
    stock as S
    left join itemmast as I on S.item = I.Code
    and I.ItemType = 'Store'
    left join voucher_type as VT on S.vtype = VT.v_type
    and S.propertyid = VT.propertyid
    left join subgroup as SG on S.partycode = SG.sub_code
    left join stock as S1 on S.contradocid = S1.docid
    and S.contrasno = S1.sno
    left join godown_mast as D on S1.godowncode = D.scode
where
    S.propertyid = 103
    and S.vdate between '2025-04-14'
    and '2025-04-14'
    and S.godowncode in ('PURC103')
    and I.ItemType = 'Store'
order by
    S.item asc,
    S.vdate asc,
    SeqNo asc,
    S.vtype asc,
    S.vno asc




    -- fixed --

    SELECT
    S.vdate,
    S.vtype,
    S.vno,
    S.amount,
    S.item,
    I.Name,
    D.name as godown,
    CASE
        WHEN S.vtype IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC')
            THEN S.recdqty
        ELSE 0
    END AS QtyRec,
    CASE
        WHEN S.vtype IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS')
            THEN S.issqty
        ELSE 0
    END AS QtyIss,
    #CASE
        #WHEN S.vtype IN ('PBC', 'PBR', 'PRR', 'PRC', 'MRE')
            #THEN SG.name
        #ELSE D.name
    #END AS Particulars,
    CASE
        WHEN S.vtype IN ('PBC', 'PBR', 'MRE', 'RQI', 'STOP', 'BKREC', 'KSREC', 'KMREC')
            THEN 'A'
        WHEN S.vtype IN ('PRR', 'PRC', 'RQR', 'BKISS', 'KSISS', 'KMISS')
            THEN 'B'
        ELSE 'C'
    END AS SeqNo
FROM
    stock AS S
    LEFT JOIN itemmast AS I
        ON S.item = I.Code
       AND I.ItemType = 'Store'
    LEFT JOIN subgroup AS SG
        ON S.partycode = SG.sub_code
    LEFT JOIN stock AS S1
        ON S.contradocid = S1.docid
       AND S.contrasno = S1.sno
    LEFT JOIN godown_mast AS D
        ON S1.godowncode = D.scode
WHERE
    S.propertyid = 103
    AND S.vdate BETWEEN '2025-04-14' AND '2025-04-14'
    AND S.godowncode IN ('PURC103')
    AND I.ItemType = 'Store'
ORDER BY
    S.item ASC,
    S.vdate ASC,
    SeqNo ASC,
    S.vtype ASC,
    S.vno ASC;