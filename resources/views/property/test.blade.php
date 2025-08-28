<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Balance Report</title>

    <!-- Bootstrap + DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <style>
        h1.report-title {
            text-align: center;
            font-size: 2rem;
            margin: 20px 0;
        }

        .dt-buttons {
            margin-bottom: 10px;
        }

        tfoot tr th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="report-title">🌟 Account Balance Report</h1>
        <table id="accountTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Account ID</th>
                    <th>Account Holder</th>
                    <th>Balance (₹)</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-end">Total</th>
                    <th id="totalBalance" class="text-end"></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            let totalBalance = 0;
            const $tbody = $('#accountTable tbody');

            for (let i = 1; i <= 100; i++) {
                const balance = Math.floor(Math.random() * 90000 + 1000);
                totalBalance += balance;
                const date = new Date(2025, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1);
                const dateStr = date.toISOString().split('T')[0];

                $tbody.append(`
        <tr>
          <td>ACCT${i.toString().padStart(4, '0')}</td>
          <td>Holder ${i}</td>
          <td class="text-end">${balance.toLocaleString()}</td>
          <td>${dateStr}</td>
        </tr>
      `);
            }

            // Show sum in tfoot
            $('#totalBalance').text(totalBalance.toLocaleString());

            $('#accountTable').DataTable({
                dom: 'Bfrtip',
                footerCallback: function(row, data, start, end, display) {
                    $(this.api().column(2).footer()).html(totalBalance.toLocaleString());
                },
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Account_Balance_Report'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: '',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        customize: function(doc) {
                            doc.content.splice(0, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: 'center',
                                fontSize: 12,
                                text: [{
                                        text: 'ANALYSIS DEMONSTRATION PACKAGE\n',
                                        bold: true,
                                        fontSize: 14
                                    },
                                    {
                                        text: 'Area No. 141/46 GT Road, Mukatpura\nRoorkee - Haridwar-249402\nTrial Balance (Ledger)\n',
                                        fontSize: 12
                                    },
                                    {
                                        text: 'From 01-Apr-2024 To 31-Mar-2025\n\n',
                                        italics: true,
                                        fontSize: 11
                                    }
                                ]
                            });

                            const dateStr = new Date().toLocaleDateString('en-GB');
                            doc.header = function() {
                                return {
                                    columns: [{
                                            text: ''
                                        },
                                        {
                                            text: '',
                                            alignment: 'center'
                                        },
                                        {
                                            text: `Date: ${dateStr}\nPage No: 1`,
                                            alignment: 'right',
                                            margin: [0, 10, 10, 0],
                                            fontSize: 9
                                        }
                                    ],
                                    margin: [10, 10, 10, 0]
                                };
                            };

                            doc.footer = function(currentPage, pageCount) {
                                if (currentPage === pageCount) {
                                    return {
                                        margin: [10, 0, 10, 20],
                                        columns: [{
                                                text: `Total Balance: ₹${totalBalance.toLocaleString()}`,
                                                bold: true,
                                                alignment: 'left'
                                            },
                                            {
                                                text: 'Generated by astrogeeksagar',
                                                alignment: 'right',
                                                fontSize: 9
                                            }
                                        ]
                                    };
                                }
                                return {};
                            };
                        }
                    },
                    {
                        extend: 'print',
                        title: '',
                        customize: function(win) {
                            $(win.document.body).prepend(`
                        <div style="text-align:center; margin-bottom:20px;">
                            <h3>ANALYSIS DEMONSTRATION PACKAGE</h3>
                            <div>Area No. 141/46 GT Road, Mukatpura</div>
                            <div>Roorkee - Haridwar-249402</div>
                            <div><strong>Trial Balance (Ledger)</strong></div>
                            <div><em>From 01-Apr-2024 To 31-Mar-2025</em></div>
                        </div>
                        `);
                                        $(win.document.body).append(`
                        <div style="margin-top:30px;">
                            <strong>Total Balance: ₹${totalBalance.toLocaleString()}</strong><br/>
                            <span style="font-size:12px;">Generated by astrogeeksagar</span>
                        </div>
                        `);
                        }
                    }
                ]
            });
        });
    </script>

</body>

</html>
