<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Results - {{ $assessment->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================================
           PROFESSIONAL PRINT DOCUMENT STYLE
           ============================================================ */
        @page {
            margin: 20mm 25mm;
            size: A4 portrait;
        }

        body {
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* 🟢 OFFICIAL HEADER */
        .header {
            text-align: center;
            border-bottom: 3px solid #0A2463;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #0A2463;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 3px 0;
            color: #475569;
            font-size: 12px;
        }

        .header .assessment-title {
            font-size: 14px;
            font-weight: 600;
            color: #0A2463;
            margin-top: 6px;
        }

        /* 🟢 HIGH-LEVEL SUMMARY TABLE */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .summary-table td {
            padding: 10px 15px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
            width: 25%;
            font-size: 12px;
        }

        .summary-table td:last-child {
            border-right: none;
        }

        .summary-table .num {
            font-size: 20px;
            font-weight: 700;
            color: #0A2463;
            display: block;
            margin-bottom: 2px;
        }

        .summary-table .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* 🟢 QUESTIONS TABLE */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #0A2463;
            margin-bottom: 12px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
        }

        .q-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 11px;
        }

        .q-table th {
            background: #f1f5f9;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            color: #0A2463;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
        }

        .q-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .q-table .q-text {
            font-weight: 500;
            width: 55%;
        }

        .q-table .q-text .avg {
            font-weight: 400;
            color: #0A2463;
            font-size: 10px;
            float: right;
            background: #eef2ff;
            padding: 1px 8px;
            border-radius: 12px;
        }

        .q-table .rating-cell {
            text-align: center;
            font-size: 11px;
            width: 8%;
        }

        .q-table .rating-cell .count-label {
            display: block;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .q-table .rating-cell.highlight {
            background: #eef2ff;
            font-weight: 600;
            color: #0A2463;
        }

        /* 🟢 COMMENTS SECTION */
        .comments-section {
            margin-top: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }

        .comments-section h4 {
            font-size: 12px;
            font-weight: 600;
            color: #0A2463;
            margin: 0 0 8px 0;
        }

        .comment-item {
            padding: 6px 0;
            border-bottom: 1px dotted #e2e8f0;
            font-size: 11px;
            color: #475569;
            line-height: 1.4;
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-item .dash {
            color: #94a3b8;
            margin-right: 6px;
        }

        /* 🟢 FOOTER */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }

        .footer .signature {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            width: 250px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 6px;
            font-size: 12px;
            color: #0A2463;
            font-weight: 600;
        }

        /* 🟢 PRINT BUTTON */
        .no-print {
            display: block;
            text-align: center;
            margin: 30px 0;
        }

        .no-print button {
            background: #0A2463;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .no-print button:hover {
            background: #1E3A8A;
            transform: translateY(-2px);
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 10px;
            }

            .q-table th,
            .q-table td {
                padding: 5px 8px;
            }

            .summary-table td {
                padding: 8px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!--  OFFICIAL HEADER -->
        <div class="header">
            <h1>Mandalay Technological University</h1>
            <p>Ministry of Science and Technology</p>
            <div class="assessment-title">Evaluation Form Report</div>
            <p>{{ $assessment->name }}</p>
            <p>{{ $assessment->year }} - {{ $assessment->semester }}</p>
        </div>

        <!-- HIGH-LEVEL SUMMARY TABLE -->
        <table class="summary-table">
            <tr>
                <td>
                    <span class="num">{{ $totalSubmissions }}</span>
                    <span class="label">Total Submissions</span>
                </td>
                <td>
                    <span class="num">{{ $uniqueStudents }}</span>
                    <span class="label">Respondents</span>
                </td>
                <td>
                    <span class="num">{{ $responseRate }}%</span>
                    <span class="label">Response Rate</span>
                </td>
                <td>
                    <span class="num">{{ number_format($overallAverage, 2) }}</span>
                    <span class="label">Overall Average (1-5)</span>
                </td>
            </tr>
        </table>

        <!-- DETAILED QUESTIONS TABLE -->
        <div class="section-title">Detailed Results by Question</div>

        <table class="q-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Evaluation Question</th>
                    <th style="width: 9%; text-align: center;">1</th>
                    <th style="width: 9%; text-align: center;">2</th>
                    <th style="width: 9%; text-align: center;">3</th>
                    <th style="width: 9%; text-align: center;">4</th>
                    <th style="width: 9%; text-align: center;">5</th>
                    <th style="width: 5%; text-align: center;">Avg</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questionData as $data)
                    @php
                        $q = $data['question'];
                        $dist = $data['distribution'];
                    @endphp

                    @if ($q->type !== 'text')
                        <tr>
                            <td class="q-text">
                                Q{{ $q->order }}. {{ $q->question_text }}
                                <span class="avg">({{ $data['count'] }} responses)</span>
                            </td>
                            <td class="rating-cell">{{ $dist[1] ?? 0 }}</td>
                            <td class="rating-cell">{{ $dist[2] ?? 0 }}</td>
                            <td class="rating-cell">{{ $dist[3] ?? 0 }}</td>
                            <td class="rating-cell">{{ $dist[4] ?? 0 }}</td>
                            <td class="rating-cell highlight">{{ $dist[5] ?? 0 }}</td>
                            <td class="rating-cell highlight">{{ number_format($data['average'], 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- COMMENTS SECTION -->
        @php
            $allComments = [];
            foreach ($questionData as $data) {
                if ($data['question']->type === 'text' && count($data['text_responses']) > 0) {
                    $allComments = array_merge($allComments, $data['text_responses']);
                }
            }
        @endphp

        @if (count($allComments) > 0)
            <div class="comments-section">
                <h4>Student Comments</h4>
                @foreach ($allComments as $comment)
                    <div class="comment-item">
                        <span class="dash">—</span> {{ $comment }}
                    </div>
                @endforeach
            </div>
        @else
            <div style="margin-top: 10px; font-style: italic; color: #94a3b8; font-size: 11px;">
                No text comments were submitted by students.
            </div>
        @endif

        <!-- FOOTER & SIGNATURE -->
        <div class="footer">
            <p>Generated on {{ \Carbon\Carbon::now()->format('d F Y, h:i A') }} &bull; MTU University Portal</p>
        </div>
    </div>

    <!-- PRINT BUTTON -->
    <div class="no-print">
        <button onclick="window.print()">
            <i class="bi bi-printer"></i> Print / Save as PDF
        </button>
        <div style="margin-top: 10px; font-size: 0.8rem; color: #64748b;">
            Or press <strong>Cmd + P</strong> (Mac) or <strong>Ctrl + P</strong> (Windows)
        </div>
    </div>
</body>

</html>
