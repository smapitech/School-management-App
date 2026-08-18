<?php include __DIR__ . '/nav.php'; ?>
<?php
    $template = (array) ($edit ?? []);
    $templateId = (int) ($template['id'] ?? 0);
    $templateTimestamp = static fn (array $row): string => trim((string) ($row['created_at'] ?? $row['updated_at'] ?? ''));
    $templateCreatedAt = $templateTimestamp($template);
    $academicYearFallback = trim((string) ($settings['academic_year'] ?? ($settings['school_session'] ?? ''))) ?: ((int) date('Y')) . '-' . ((int) date('Y') + 1);
    $templateCodeFallback = 'MT-' . str_pad((string) max(1, $templateId > 0 ? $templateId : (count($templates) + 1)), 3, '0', STR_PAD_LEFT);
    $value = static fn (string $key, mixed $default = ''): string => trim((string) ($template[$key] ?? $default));
    $checked = static fn (string $key, int $default = 1): string => (int) ($template[$key] ?? $default) === 1 ? 'checked' : '';
    $selected = static fn (string $key, mixed $option, mixed $default = ''): string => (string) ($template[$key] ?? $default) === (string) $option ? 'selected' : '';
    $show = static fn (string $key, int $default = 1): bool => (int) ($template[$key] ?? $default) === 1;
    $formatTemplateDate = static function (mixed $value): string {
        $date = trim((string) $value);
        if ($date === '') {
            return 'Not recorded';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return 'Not recorded';
        }

        return e(date('M d, Y', $timestamp));
    };
    $formatNumber = static function (mixed $value, int $precision = 2): string {
        if ($value === null || $value === '') {
            return '-';
        }

        $number = (float) $value;
        $formatted = number_format($number, $precision, '.', '');

        return rtrim(rtrim($formatted, '0'), '.');
    };
    $spellPreviewNumber = static function (int $number): string {
        $number = abs($number);
        if ($number === 0) {
            return 'Zero';
        }

        $units = [
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
        ];
        $teens = [
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
        ];
        $tens = [
            2 => 'Twenty',
            3 => 'Thirty',
            4 => 'Forty',
            5 => 'Fifty',
            6 => 'Sixty',
            7 => 'Seventy',
            8 => 'Eighty',
            9 => 'Ninety',
        ];
        $spellBelowThousand = static function (int $value) use ($units, $teens, $tens): string {
            $parts = [];

            if ($value >= 100) {
                $parts[] = $units[intdiv($value, 100)] . ' Hundred';
                $value %= 100;
            }

            if ($value >= 20) {
                $parts[] = $tens[intdiv($value, 10)] ?? '';
                $remainder = $value % 10;
                if ($remainder > 0) {
                    $parts[] = $units[$remainder] ?? '';
                }
            } elseif ($value >= 10) {
                $parts[] = $teens[$value] ?? '';
            } elseif ($value > 0) {
                $parts[] = $units[$value] ?? '';
            }

            return trim(implode(' ', array_filter($parts)));
        };

        $parts = [];
        if ($number >= 1000) {
            $thousands = intdiv($number, 1000);
            $parts[] = $spellBelowThousand($thousands) . ' Thousand';
            $number %= 1000;
        }

        if ($number > 0) {
            $parts[] = $spellBelowThousand($number);
        }

        return trim(implode(' ', array_filter($parts)));
    };
    $paperSizes = ['A4', 'A5', 'Letter', 'Legal'];
    $orientations = ['Portrait', 'Landscape'];
    $logoPositions = ['Top Center', 'Top Left', 'Top Right'];
    $fontFamilies = [
        'Arial, Helvetica, sans-serif',
        'Georgia, serif',
        'Tahoma, sans-serif',
        '"Times New Roman", Times, serif',
        'Verdana, sans-serif',
    ];
    $biodataLayouts = ['Table', 'Grid', 'Stacked'];
    $statusOptions = ['Active', 'Draft', 'Inactive'];
    $distributionRows = array_values((array) ($distributions ?? []));
    $gradeRows = array_values((array) ($grades ?? []));
    $subjectRows = array_slice(array_values((array) ($subjects ?? [])), 0, 5);
    $defaultHeader = '<div class="template-default-header"><h2>[school_name]</h2><p>[school_address]</p><h3>Student Report Sheet</h3></div>';
    $defaultFooter = '<p>Principal Comment: [principal_comment]</p><p>Teacher Comment: [teacher_comment]</p>';
    $headerContent = trim((string) ($template['header_content'] ?? '')) !== '' ? (string) $template['header_content'] : $defaultHeader;
    $footerContent = trim((string) ($template['footer_content'] ?? '')) !== '' ? (string) $template['footer_content'] : $defaultFooter;
    $schoolName = trim((string) ($settings['school_name'] ?? ($settings['name'] ?? 'Smapis School Portal')));
    $schoolAddress = trim((string) ($settings['school_address'] ?? ($settings['address'] ?? '')));
    $schoolPhone = trim((string) ($settings['school_phone'] ?? ($settings['phone'] ?? '')));
    $schoolEmail = trim((string) ($settings['school_email'] ?? ($settings['email'] ?? '')));
    $schoolShortName = trim((string) ($settings['school_short_name'] ?? ($settings['short_name'] ?? 'SM')));
    $schoolLogoPath = trim((string) ($template['logo'] ?? '')) ?: trim((string) ($settings['logo_path'] ?? ($settings['logo'] ?? '')));
    $schoolLogoUrl = $schoolLogoPath !== '' ? public_upload_url($schoolLogoPath) : '';
    $schoolPhotoFallback = strtoupper(substr($schoolShortName !== '' ? $schoolShortName : $schoolName, 0, 2));
    $previewStudent = [
        'student_name' => 'Sample Student',
        'register_no' => 'REG-2026-014',
        'roll_number' => '23',
        'father_name' => 'Sample Parent',
        'mother_name' => 'Sample Parent',
        'admission_date' => '14 May 2020',
        'date_of_birth' => '22 Sep 2014',
        'class' => trim((string) ($template['class_name'] ?? 'Primary 5')) ?: 'Primary 5',
        'section' => trim((string) ($template['section'] ?? 'A')) ?: 'A',
        'gender' => 'Male',
    ];
    $previewPhotoInitials = 'SS';
    $previewDistributions = $distributionRows ?: [
        ['name' => 'CA Test 1', 'max_mark' => 20],
        ['name' => 'CA Test 2', 'max_mark' => 20],
        ['name' => 'Examination', 'max_mark' => 60],
    ];
    $previewGradeRows = $gradeRows ?: [
        ['grade_name' => 'A', 'min_mark' => 75, 'max_mark' => 100, 'remark' => 'Excellent'],
        ['grade_name' => 'B', 'min_mark' => 65, 'max_mark' => 74, 'remark' => 'Very Good'],
        ['grade_name' => 'C', 'min_mark' => 50, 'max_mark' => 64, 'remark' => 'Good'],
        ['grade_name' => 'D', 'min_mark' => 40, 'max_mark' => 49, 'remark' => 'Fair'],
        ['grade_name' => 'F', 'min_mark' => 0, 'max_mark' => 39, 'remark' => 'Fail'],
    ];
    $previewGradePoints = [];
    $previewGradeCount = count($previewGradeRows);
    foreach ($previewGradeRows as $index => $gradeRow) {
        $gradeName = strtoupper(trim((string) ($gradeRow['grade_name'] ?? '')));
        if ($gradeName === '') {
            continue;
        }

        $previewGradePoints[$gradeName] = $index >= $previewGradeCount - 1 ? 0.0 : (float) max(1, 5 - $index);
    }
    $previewGradeForPercentage = static function (float $percentage) use ($previewGradeRows, $previewGradePoints): array {
        foreach ($previewGradeRows as $gradeRow) {
            $min = (float) ($gradeRow['min_mark'] ?? 0);
            $max = (float) ($gradeRow['max_mark'] ?? 0);
            if ($percentage >= $min && $percentage <= $max) {
                $gradeName = trim((string) ($gradeRow['grade_name'] ?? ''));
                return [
                    'grade' => $gradeName,
                    'remark' => trim((string) ($gradeRow['remark'] ?? '')),
                    'point' => (float) ($previewGradePoints[strtoupper($gradeName)] ?? 0),
                ];
            }
        }

        return [
            'grade' => '',
            'remark' => '',
            'point' => 0.0,
        ];
    };
    $distributionRatios = [0.78, 0.84, 0.91, 0.67, 0.73, 0.88];
    $subjectRatios = [0.94, 0.82, 0.76, 0.89, 0.71, 0.85];
    $previewSubjectRows = [];

    foreach (($subjectRows ?: [
        ['subject_name' => 'English'],
        ['subject_name' => 'Mathematics'],
        ['subject_name' => 'Science'],
        ['subject_name' => 'Social Studies'],
        ['subject_name' => 'Computer'],
    ]) as $subjectIndex => $subjectRow) {
        $cells = [];
        $total = 0.0;
        $obtainable = 0.0;
        $subjectRatio = $subjectRatios[$subjectIndex % count($subjectRatios)];

        foreach ($previewDistributions as $distributionIndex => $distribution) {
            $maxMark = max(0.0, (float) ($distribution['max_mark'] ?? 0));
            $distributionRatio = $distributionRatios[$distributionIndex % count($distributionRatios)];
            $score = $maxMark > 0 ? min($maxMark, round($maxMark * $distributionRatio * $subjectRatio)) : 0.0;
            $cells[] = [
                'title' => trim((string) ($distribution['name'] ?? '')),
                'score' => $score,
                'max' => $maxMark,
            ];
            $total += $score;
            $obtainable += $maxMark;
        }

        $percentage = $obtainable > 0 ? ($total / $obtainable) * 100 : 0.0;
        $gradeInfo = $previewGradeForPercentage($percentage);
        $previewSubjectRows[] = [
            'subject' => trim((string) ($subjectRow['subject_name'] ?? ($subjectRow['name'] ?? 'Subject'))),
            'cells' => $cells,
            'total' => $total,
            'obtainable' => $obtainable,
            'percentage' => $percentage,
            'grade' => $gradeInfo['grade'],
            'point' => (float) ($gradeInfo['point'] ?? 0),
            'remark' => $gradeInfo['remark'],
            'position' => $subjectIndex + 1,
        ];
    }

    $previewGrandTotal = array_sum(array_map(static fn (array $row): float => (float) ($row['total'] ?? 0), $previewSubjectRows));
    $previewObtainable = array_sum(array_map(static fn (array $row): float => (float) ($row['obtainable'] ?? 0), $previewSubjectRows));
    $previewAverage = $previewObtainable > 0 ? ($previewGrandTotal / $previewObtainable) * 100 : 0.0;
    $previewGpa = count($previewSubjectRows) > 0
        ? array_sum(array_map(static fn (array $row): float => (float) ($row['point'] ?? 0), $previewSubjectRows)) / count($previewSubjectRows)
        : 0.0;
    $previewResultStatus = $previewAverage >= (float) ($template['pass_mark'] ?? 40)
        ? trim((string) ($template['pass_label'] ?? 'Pass'))
        : trim((string) ($template['fail_label'] ?? 'Fail'));
    $previewGrandTotalWords = $spellPreviewNumber((int) round($previewGrandTotal));
    $attendanceAvailable = $show('show_attendance', 1);
    $previewAttendanceWorkingDays = $attendanceAvailable ? 200 : null;
    $previewAttendanceAttended = $attendanceAvailable ? 186 : null;
    $previewAttendancePercentage = $attendanceAvailable && $previewAttendanceWorkingDays > 0
        ? round(($previewAttendanceAttended / $previewAttendanceWorkingDays) * 100, 2)
        : null;
    $previewPrincipalComment = 'Keep up the excellent academic effort and continue to lead by example.';
    $previewTeacherComment = 'Strong class participation and steady progress across core subjects.';
    $previewPrincipalSignature = trim((string) ($template['left_signature'] ?? ''));
    $previewTeacherSignature = trim((string) ($template['middle_signature'] ?? ''));
    $previewStamp = trim((string) ($template['right_signature'] ?? ''));
    $columnRows = [];
    $columnRows[] = [
        'title' => 'Subject',
        'key' => 'subject',
        'type' => 'Text',
        'max' => '-',
        'pass' => '-',
        'width' => '22',
        'align' => 'Left',
        'visible' => $show('show_subject_name', 1),
        'source' => 'Core',
    ];
    foreach ($previewDistributions as $distributionIndex => $distribution) {
        $maxMark = max(0.0, (float) ($distribution['max_mark'] ?? 0));
        $passingMark = $maxMark > 0 ? round($maxMark * ((float) ($template['pass_mark'] ?? 40) / 100)) : 0;
        $columnRows[] = [
            'title' => trim((string) ($distribution['name'] ?? 'Distribution')),
            'key' => strtolower(preg_replace('/[^a-z0-9]+/i', '_', trim((string) ($distribution['name'] ?? 'distribution')))),
            'type' => 'Number',
            'max' => $formatNumber($maxMark, $maxMark == floor($maxMark) ? 0 : 1),
            'pass' => $formatNumber($passingMark, 0),
            'width' => (string) max(6, (int) round(100 / max(1, count($previewDistributions) + 5))),
            'align' => 'Center',
            'visible' => true,
            'source' => 'Mark Distribution',
        ];
    }
    $columnRows[] = [
        'title' => 'Total',
        'key' => 'total',
        'type' => 'Number',
        'max' => '-',
        'pass' => '-',
        'width' => '10',
        'align' => 'Center',
        'visible' => $show('show_total', 1),
        'source' => 'Summary',
    ];
    $columnRows[] = [
        'title' => 'Grade',
        'key' => 'grade',
        'type' => 'Text',
        'max' => '-',
        'pass' => '-',
        'width' => '8',
        'align' => 'Center',
        'visible' => $show('show_grade', 1),
        'source' => 'Summary',
    ];
    $columnRows[] = [
        'title' => 'Point',
        'key' => 'point',
        'type' => 'Number',
        'max' => '-',
        'pass' => '-',
        'width' => '7',
        'align' => 'Center',
        'visible' => $show('show_grade_point', 1),
        'source' => 'Summary',
    ];
    $columnRows[] = [
        'title' => 'Remark',
        'key' => 'remark',
        'type' => 'Text',
        'max' => '-',
        'pass' => '-',
        'width' => '14',
        'align' => 'Left',
        'visible' => $show('show_remark', 1),
        'source' => 'Summary',
    ];
    if ($show('show_class_average', 1)) {
        $columnRows[] = [
            'title' => 'Class Avg',
            'key' => 'class_average',
            'type' => 'Number',
            'max' => '-',
            'pass' => '-',
            'width' => '10',
            'align' => 'Center',
            'visible' => true,
            'source' => 'Class Summary',
        ];
    }
    if ($show('show_subject_position', 1)) {
        $columnRows[] = [
            'title' => 'Position',
            'key' => 'position',
            'type' => 'Number',
            'max' => '-',
            'pass' => '-',
            'width' => '10',
            'align' => 'Center',
            'visible' => true,
            'source' => 'Class Summary',
        ];
    }
?>



<style>
/* Smapis Marksheet Template Builder verified 65/35 layout refinement - 2026-07-24
   Exact 65% editor / 35% preview balance, compact readable tables, and overflow-safe preview. */
.marksheet-builder {
    width: 100% !important;
    max-width: none !important;
    overflow-x: hidden !important;
    box-sizing: border-box !important;
}

.marksheet-builder *,
.marksheet-builder *::before,
.marksheet-builder *::after {
    box-sizing: border-box !important;
}

.marksheet-builder-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 65fr) minmax(360px, 35fr) !important; /* verified 65% / 35% */
    gap: 18px !important;
    align-items: start !important;
    width: 100% !important;
    max-width: 100% !important;
}

.marksheet-editor,
.marksheet-template-editor,
.marksheet-builder-form,
.marksheet-section-card {
    min-width: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* Editor/result-column table: compact, readable, and not oversized. */
.marksheet-column-table-wrap {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
}

.marksheet-column-table {
    width: 100% !important;
    min-width: 720px !important;
    table-layout: fixed !important;
    font-size: 11.4px !important;
}

.marksheet-column-table th,
.marksheet-column-table td {
    padding: 7px 6px !important;
    font-size: 11.4px !important;
    line-height: 1.22 !important;
    vertical-align: middle !important;
    overflow-wrap: anywhere !important;
    word-break: break-word !important;
}

.marksheet-column-table th:first-child,
.marksheet-column-table td:first-child {
    width: 38px !important;
    text-align: center !important;
}

.marksheet-column-table th:nth-child(2),
.marksheet-column-table td:nth-child(2) {
    width: 15% !important;
}

.marksheet-column-table th:nth-child(3),
.marksheet-column-table td:nth-child(3) {
    width: 13% !important;
}

.marksheet-column-table th:nth-child(4),
.marksheet-column-table td:nth-child(4) {
    width: 10% !important;
}

.marksheet-column-table th:nth-child(5),
.marksheet-column-table td:nth-child(5),
.marksheet-column-table th:nth-child(6),
.marksheet-column-table td:nth-child(6),
.marksheet-column-table th:nth-child(7),
.marksheet-column-table td:nth-child(7) {
    width: 8% !important;
}

.marksheet-column-table code,
.marksheet-column-table .status {
    font-size: 10.8px !important;
}

/* Live preview: 35% of workspace, readable, and locked inside its border. */
.marksheet-preview-panel {
    width: 100% !important;
    max-width: none !important;
    min-width: 0 !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
}

.marksheet-preview-panel .marksheet-section-head {
    gap: 8px !important;
    margin-bottom: 10px !important;
}

.marksheet-preview-panel .marksheet-section-head h3 {
    font-size: 15px !important;
    line-height: 1.2 !important;
}

.marksheet-preview-panel .report-sheet-note {
    font-size: 11px !important;
    line-height: 1.35 !important;
}

.marksheet-preview-sheet {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
    padding: 12px !important;
    border-radius: 12px !important;
    font-size: 8.5px !important;
    line-height: 1.25 !important;
}

.marksheet-preview-header {
    display: grid !important;
    grid-template-columns: 42px minmax(0, 1fr) 44px !important;
    gap: 7px !important;
    align-items: center !important;
    margin-bottom: 8px !important;
}

.marksheet-preview-brand img,
.marksheet-preview-logo-placeholder {
    width: 38px !important;
    height: 38px !important;
    max-width: 38px !important;
    max-height: 38px !important;
    object-fit: contain !important;
}

.marksheet-preview-heading {
    min-width: 0 !important;
    overflow: hidden !important;
    text-align: center !important;
}

.marksheet-preview-heading strong {
    display: block !important;
    font-size: 10.6px !important;
    line-height: 1.1 !important;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
}

.marksheet-preview-heading span,
.marksheet-preview-heading small {
    display: block !important;
    font-size: 7.4px !important;
    line-height: 1.2 !important;
    overflow-wrap: anywhere !important;
}

.marksheet-preview-photo,
.marksheet-preview-photo-placeholder {
    width: 42px !important;
    height: 48px !important;
    max-width: 42px !important;
    max-height: 48px !important;
}

.marksheet-preview-title {
    margin: 5px 0 7px !important;
    text-align: center !important;
}

.marksheet-preview-title strong {
    font-size: 10.4px !important;
}

.marksheet-preview-title span {
    font-size: 7.4px !important;
}

.marksheet-preview-biodata,
.marksheet-preview-table,
.marksheet-preview-box table {
    width: 100% !important;
    max-width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
}

.marksheet-preview-biodata {
    margin-bottom: 8px !important;
}

.marksheet-preview-biodata td {
    padding: 3px 4px !important;
    font-size: 7.6px !important;
    line-height: 1.2 !important;
    overflow-wrap: anywhere !important;
}

.marksheet-preview-biodata td:nth-child(1),
.marksheet-preview-biodata td:nth-child(3) {
    width: 24% !important;
    font-weight: 700 !important;
}

.marksheet-preview-biodata td:nth-child(2),
.marksheet-preview-biodata td:nth-child(4) {
    width: 26% !important;
}

.marksheet-preview-table-wrap {
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

.marksheet-preview-table {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    table-layout: fixed !important;
    font-size: 8px !important;
}

.marksheet-preview-table th,
.marksheet-preview-table td {
    padding: 3px 1.5px !important;
    font-size: 8px !important;
    line-height: 1.1 !important;
    text-align: center !important;
    vertical-align: middle !important;
    white-space: normal !important;
    overflow: hidden !important;
    overflow-wrap: anywhere !important;
    word-break: break-word !important;
}

.marksheet-preview-table th small {
    font-size: 6.6px !important;
    line-height: 1 !important;
}

/* First/Subject column reduced; same readable font size as other preview cells. */
.marksheet-preview-table .is-subject {
    width: 15% !important;
    max-width: 15% !important;
    text-align: left !important;
    font-size: 8px !important;
}

.marksheet-preview-table .is-remark {
    width: 10% !important;
    max-width: 10% !important;
    text-align: left !important;
    font-size: 8px !important;
}

.marksheet-preview-table .is-center {
    width: auto !important;
    font-size: 8px !important;
}

.marksheet-preview-summary-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 5px !important;
    margin-top: 8px !important;
}

.marksheet-preview-summary {
    min-width: 0 !important;
    padding: 5px 6px !important;
}

.marksheet-preview-summary span {
    font-size: 7.1px !important;
}

.marksheet-preview-summary strong {
    font-size: 8.5px !important;
    overflow-wrap: anywhere !important;
}

.marksheet-preview-split {
    display: grid !important;
    grid-template-columns: .9fr 1.1fr !important;
    gap: 6px !important;
    margin-top: 8px !important;
}

.marksheet-preview-box {
    min-width: 0 !important;
    padding: 7px !important;
    overflow: hidden !important;
}

.marksheet-preview-box h4 {
    margin: 0 0 5px !important;
    font-size: 8.6px !important;
}

.marksheet-preview-box dl,
.marksheet-preview-box table,
.marksheet-preview-box dt,
.marksheet-preview-box dd,
.marksheet-preview-box th,
.marksheet-preview-box td {
    font-size: 7.3px !important;
    line-height: 1.15 !important;
    overflow-wrap: anywhere !important;
}

.marksheet-preview-comments,
.marksheet-preview-signatures {
    margin-top: 8px !important;
}

.marksheet-preview-comments {
    gap: 5px !important;
}

.marksheet-preview-comments div {
    padding: 5px !important;
    font-size: 7.3px !important;
}

.marksheet-preview-signatures {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 7px !important;
}

.marksheet-preview-signatures figure,
.marksheet-preview-signatures div {
    min-width: 0 !important;
    font-size: 7.1px !important;
    text-align: center !important;
}

.marksheet-preview-signatures img {
    max-width: 52px !important;
    max-height: 34px !important;
    object-fit: contain !important;
    margin-inline: auto !important;
}

.marksheet-preview-footer {
    margin-top: 7px !important;
    font-size: 7px !important;
    overflow-wrap: anywhere !important;
}

@media (min-width: 1500px) {
    .marksheet-builder-grid {
        grid-template-columns: minmax(0, 65fr) minmax(420px, 35fr) !important;
        gap: 20px !important;
    }

    .marksheet-preview-table th,
    .marksheet-preview-table td,
    .marksheet-preview-table .is-subject,
    .marksheet-preview-table .is-remark,
    .marksheet-preview-table .is-center {
        font-size: 8.2px !important;
    }
}

@media (max-width: 1280px) {
    .marksheet-builder-grid {
        grid-template-columns: 1fr !important;
    }

    .marksheet-preview-panel {
        max-width: 100% !important;
    }

    .marksheet-preview-sheet {
        max-width: 760px !important;
        margin-inline: auto !important;
    }
}

@media (max-width: 720px) {
    .marksheet-column-table {
        min-width: 680px !important;
    }

    .marksheet-preview-sheet {
        padding: 9px !important;
    }

    .marksheet-preview-header {
        grid-template-columns: 34px minmax(0, 1fr) 38px !important;
    }

    .marksheet-preview-split,
    .marksheet-preview-signatures {
        grid-template-columns: 1fr !important;
    }
}
</style>

<section class="marksheet-builder">
    <div class="marksheet-builder-header panel">
        <div class="marksheet-builder-copy">
            <a class="marksheet-builder-back" href="/exams">Back to Exams</a>
            <p class="eyebrow">Report sheet design</p>
            <h2>Marksheet Template Builder</h2>
            <p>Configure how student report sheets appear before printing.</p>
            <div class="marksheet-builder-meta">
                <span><strong>Mode</strong><?= $templateId > 0 ? 'Editing template' : 'Creating new template' ?></span>
                <span><strong>Status</strong><?= e($value('status', 'Active')) ?></span>
                <span><strong>Default</strong><?= (int) ($template['is_default'] ?? 0) === 1 ? 'Yes' : 'No' ?></span>
                <span><strong>Saved</strong><?= $formatTemplateDate($templateCreatedAt) ?></span>
            </div>
        </div>
        <div class="marksheet-builder-actions">
            <?php if ($templateId > 0): ?>
                <a class="secondary-action" href="/exams/marksheet-templates/preview?id=<?= e((string) $templateId) ?>">Preview</a>
            <?php endif; ?>
            <a class="secondary-action" href="/exams/result-preview">Preview With Real Student Result</a>
            <?php if (can('exams', 'edit')): ?>
                <button type="submit" form="marksheet-template-form" class="primary-action">Save Template</button>
                <?php if ($templateId > 0): ?>
                    <button
                        type="submit"
                        form="marksheet-template-form"
                        formaction="/exams/marksheet-templates/default"
                        formmethod="post"
                        formnovalidate
                        class="secondary-action"
                    >Set as Default</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <nav class="marksheet-builder-tabs" aria-label="Marksheet builder sections">
        <a href="#general-settings"><span>1</span> General Settings</a>
        <a href="#page-size-spacing"><span>2</span> Page Size &amp; Spacing</a>
        <a href="#header-logo"><span>3</span> Header &amp; Logo</a>
        <a href="#student-information"><span>4</span> Student Information</a>
        <a href="#result-table"><span>5</span> Result Table</a>
        <a href="#attendance"><span>6</span> Attendance</a>
        <a href="#grading-scale"><span>7</span> Grading Scale</a>
        <a href="#comments"><span>8</span> Comments</a>
        <a href="#signatures-stamp"><span>9</span> Signatures &amp; Stamp</a>
        <a href="#footer-settings"><span>10</span> Footer &amp; Print Settings</a>
    </nav>

    <form id="marksheet-template-form" class="marksheet-form marksheet-template-editor marksheet-builder-form" method="post" action="/exams/marksheet-templates/save" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= e((string) $templateId) ?>">

        <div class="marksheet-builder-grid">
            <div class="marksheet-editor">
                <section class="marksheet-section-card" id="general-settings">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">General settings</p>
                            <h3>Template Details</h3>
                            <p class="report-sheet-note">Name the template and keep the session, code, status, and default state aligned with this school year.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label>
                            <span>Template Name</span>
                            <input type="text" name="name" value="<?= e($value('name', 'Classic Academic Report Sheet')) ?>" required>
                        </label>
                        <label>
                            <span>Academic Session</span>
                            <input type="text" name="academic_session" value="<?= e($value('academic_session', $academicYearFallback)) ?>" placeholder="<?= e($academicYearFallback) ?>">
                        </label>
                        <label>
                            <span>Template Code</span>
                            <input type="text" name="template_code" value="<?= e($value('template_code', $templateCodeFallback)) ?>" placeholder="<?= e($templateCodeFallback) ?>">
                        </label>
                        <label>
                            <span>Status</span>
                            <select name="status">
                                <?php foreach ($statusOptions as $statusOption): ?>
                                    <option value="<?= e($statusOption) ?>" <?= $selected('status', $statusOption, 'Active') ?>><?= e($statusOption) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="form-wide">
                            <span>Description</span>
                            <textarea name="description" rows="3" placeholder="Add a short note describing when this template should be used."><?= e($value('description', 'Professional report sheet builder for Smapis School Portal')) ?></textarea>
                        </label>
                        <label class="toggle-field form-wide">
                            <input type="checkbox" name="is_default" value="1" <?= $checked('is_default', 0) ?>>
                            Set as the default template for new previews
                        </label>
                    </div>
                </section>

                <section class="marksheet-section-card" id="page-size-spacing">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Layout settings</p>
                            <h3>Page Size &amp; Spacing</h3>
                            <p class="report-sheet-note">Control page dimensions, spacing, and typography before the report sheet is printed.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label>
                            <span>Paper Size</span>
                            <select name="paper_size">
                                <?php foreach ($paperSizes as $paperSize): ?>
                                    <option value="<?= e($paperSize) ?>" <?= $selected('paper_size', $paperSize, 'A4') ?>><?= e($paperSize) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Orientation</span>
                            <select name="orientation">
                                <?php foreach ($orientations as $orientation): ?>
                                    <option value="<?= e($orientation) ?>" <?= $selected('orientation', $orientation, 'Portrait') ?>><?= e($orientation) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Page Width (px)</span>
                            <input type="number" name="page_width" value="<?= e((int) $value('page_width', 794)) ?>">
                        </label>
                        <label>
                            <span>Page Height (px)</span>
                            <input type="number" name="page_height" value="<?= e((int) $value('page_height', 1123)) ?>">
                        </label>
                        <label>
                            <span>Top Space (px)</span>
                            <input type="number" name="top_space" value="<?= e((int) $value('top_space', 30)) ?>">
                        </label>
                        <label>
                            <span>Bottom Space (px)</span>
                            <input type="number" name="bottom_space" value="<?= e((int) $value('bottom_space', 30)) ?>">
                        </label>
                        <label>
                            <span>Left Space (px)</span>
                            <input type="number" name="left_space" value="<?= e((int) $value('left_space', 30)) ?>">
                        </label>
                        <label>
                            <span>Right Space (px)</span>
                            <input type="number" name="right_space" value="<?= e((int) $value('right_space', 30)) ?>">
                        </label>
                        <label>
                            <span>Font Family</span>
                            <select name="font_family">
                                <?php foreach ($fontFamilies as $fontFamily): ?>
                                    <option value="<?= e($fontFamily) ?>" <?= $selected('font_family', $fontFamily, 'Arial, Helvetica, sans-serif') ?>><?= e($fontFamily) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Base Font Size (px)</span>
                            <input type="number" name="base_font_size" value="<?= e((int) $value('base_font_size', 12)) ?>">
                        </label>
                        <label>
                            <span>Table Font Size (px)</span>
                            <input type="number" name="table_font_size" value="<?= e((int) $value('table_font_size', 11)) ?>">
                        </label>
                        <label>
                            <span>Header Font Size (px)</span>
                            <input type="number" name="header_font_size" value="<?= e((int) $value('header_font_size', 18)) ?>">
                        </label>
                        <label>
                            <span>Line Height</span>
                            <input type="text" name="line_height" value="<?= e($value('line_height', 1.35)) ?>">
                        </label>
                    </div>
                </section>

                <section class="marksheet-section-card" id="header-logo">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Branding</p>
                            <h3>Header &amp; Logo</h3>
                            <p class="report-sheet-note">Choose how the school brand and page styling should appear on the printed report sheet.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_school_logo" value="1" <?= $checked('show_school_logo', 1) ?>>
                            Show School Logo
                        </label>
                        <label>
                            <span>Logo Position</span>
                            <select name="logo_position">
                                <?php foreach ($logoPositions as $logoPosition): ?>
                                    <option value="<?= e($logoPosition) ?>" <?= $selected('logo_position', $logoPosition, 'Top Center') ?>><?= e($logoPosition) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Logo Width (px)</span>
                            <input type="number" name="logo_width" value="<?= e((int) $value('logo_width', 420)) ?>">
                        </label>
                        <label>
                            <span>Logo Height</span>
                            <input type="text" name="logo_height" value="<?= e($value('logo_height', 'auto')) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_school_name" value="1" <?= $checked('show_school_name', 1) ?>>
                            Show School Name
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_school_address" value="1" <?= $checked('show_school_address', 1) ?>>
                            Show School Address
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_school_phone" value="1" <?= $checked('show_school_phone', 1) ?>>
                            Show School Phone
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_school_email" value="1" <?= $checked('show_school_email', 1) ?>>
                            Show School Email
                        </label>
                        <label>
                            <span>Header Height (px)</span>
                            <input type="number" name="header_height" value="<?= e((int) $value('header_height', 150)) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_border" value="1" <?= $checked('show_border', 1) ?>>
                            Show Border
                        </label>
                        <label>
                            <span>Border Width (px)</span>
                            <input type="number" name="border_width" value="<?= e((int) $value('border_width', 2)) ?>">
                        </label>
                        <label>
                            <span>Border Colour</span>
                            <input type="color" name="border_color" value="<?= e($value('border_color', '#222222')) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="inner_border" value="1" <?= $checked('inner_border', 1) ?>>
                            Inner Border
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_watermark" value="1" <?= $checked('show_watermark', 0) ?>>
                            Show Watermark
                        </label>
                        <label>
                            <span>Watermark Opacity</span>
                            <input type="text" name="watermark_opacity" value="<?= e($value('watermark_opacity', 0.06)) ?>">
                        </label>
                        <label>
                            <span>Watermark Size (%)</span>
                            <input type="number" name="watermark_size" value="<?= e((int) $value('watermark_size', 70)) ?>">
                        </label>
                        <label class="form-wide">
                            <span>Logo Image</span>
                            <div class="marksheet-upload-card">
                                <?= $schoolLogoUrl !== '' ? '<img src="' . e($schoolLogoUrl) . '" alt="Current logo preview">' : '<div class="marksheet-upload-placeholder">No logo uploaded</div>' ?>
                                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </label>
                        <label class="form-wide">
                            <span>Background Image</span>
                            <div class="marksheet-upload-card">
                                <?php if (!empty($template['background'])): ?>
                                    <img src="<?= e(public_upload_url((string) $template['background'])) ?>" alt="Current background preview">
                                <?php else: ?>
                                    <div class="marksheet-upload-placeholder">No background uploaded</div>
                                <?php endif; ?>
                                <input type="file" name="background" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </label>
                        <label class="form-wide">
                            <span>Watermark Image</span>
                            <div class="marksheet-upload-card">
                                <?php if (!empty($template['watermark_image'])): ?>
                                    <img src="<?= e(public_upload_url((string) $template['watermark_image'])) ?>" alt="Current watermark preview">
                                <?php else: ?>
                                    <div class="marksheet-upload-placeholder">No watermark uploaded</div>
                                <?php endif; ?>
                                <input type="file" name="watermark_image" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </label>
                    </div>
                </section>

                <section class="marksheet-section-card" id="student-information">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Student profile</p>
                            <h3>Student Information</h3>
                            <p class="report-sheet-note">Identity fields like student name, class, and section are always shown. Toggle the optional biodata fields here.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_student_photo" value="1" <?= $checked('show_student_photo', 1) ?>>
                            Show Student Photo
                        </label>
                        <label>
                            <span>Student Photo Width (px)</span>
                            <input type="number" name="student_photo_width" value="<?= e((int) $value('student_photo_width', 110)) ?>">
                        </label>
                        <label>
                            <span>Student Photo Height (px)</span>
                            <input type="number" name="student_photo_height" value="<?= e((int) $value('student_photo_height', 130)) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_biodata_section" value="1" <?= $checked('show_biodata_section', 1) ?>>
                            Show Biodata Section
                        </label>
                        <label>
                            <span>Biodata Layout</span>
                            <select name="biodata_layout">
                                <?php foreach ($biodataLayouts as $layout): ?>
                                    <option value="<?= e($layout) ?>" <?= $selected('biodata_layout', $layout, 'Table') ?>><?= e($layout) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_register_number" value="1" <?= $checked('show_register_number', 1) ?>>
                            Show Register Number
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_roll_number" value="1" <?= $checked('show_roll_number', 0) ?>>
                            Show Roll Number
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_father_name" value="1" <?= $checked('show_father_name', 1) ?>>
                            Show Father Name
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_mother_name" value="1" <?= $checked('show_mother_name', 1) ?>>
                            Show Mother Name
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_admission_date" value="1" <?= $checked('show_admission_date', 1) ?>>
                            Show Admission Date
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_date_of_birth" value="1" <?= $checked('show_date_of_birth', 1) ?>>
                            Show Date of Birth
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_gender" value="1" <?= $checked('show_gender', 1) ?>>
                            Show Gender
                        </label>
                        <div class="form-wide marksheet-note-box">
                            Student name, class, and section are always included in the report sheet preview and final output.
                        </div>
                    </div>
                </section>

                <section class="marksheet-section-card" id="result-table">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Result columns</p>
                            <h3>Result Table</h3>
                            <p class="report-sheet-note">Dynamic mark columns come from the Mark Distribution module. The table below previews the current column order.</p>
                        </div>
                        <div class="panel-actions">
                            <a class="secondary-action" href="/exams/distribution">Manage Mark Distribution</a>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_subject_table" value="1" <?= $checked('show_subject_table', 1) ?>>
                            Show Subject Table
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_subject_name" value="1" <?= $checked('show_subject_name', 1) ?>>
                            Show Subject Name
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_dynamic_mark_distribution_columns" value="1" <?= $checked('show_dynamic_mark_distribution_columns', 1) ?>>
                            Show Dynamic Columns
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_total" value="1" <?= $checked('show_total', 1) ?>>
                            Show Total
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_grade" value="1" <?= $checked('show_grade', 1) ?>>
                            Show Grade
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_grade_point" value="1" <?= $checked('show_grade_point', 1) ?>>
                            Show Grade Point
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_remark" value="1" <?= $checked('show_remark', 1) ?>>
                            Show Remark
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_subject_position" value="1" <?= $checked('show_subject_position', 1) ?>>
                            Show Subject Position
                        </label>
                        <label>
                            <span>Table Border Colour</span>
                            <input type="color" name="table_border_color" value="<?= e($value('table_border_color', '#222222')) ?>">
                        </label>
                        <label>
                            <span>Table Header Background</span>
                            <input type="color" name="table_header_background" value="<?= e($value('table_header_background', '#eaf2ef')) ?>">
                        </label>
                    </div>
                    <div class="marksheet-column-table-wrap">
                        <table class="marksheet-column-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Column Title</th>
                                    <th>Column Key</th>
                                    <th>Type</th>
                                    <th>Max Marks</th>
                                    <th>Passing Marks</th>
                                    <th>Width %</th>
                                    <th>Align</th>
                                    <th>Visible</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($columnRows as $index => $columnRow): ?>
                                    <tr>
                                        <td><?= e((string) ($index + 1)) ?></td>
                                        <td><?= e((string) ($columnRow['title'] ?? '')) ?></td>
                                        <td><code><?= e((string) ($columnRow['key'] ?? '')) ?></code></td>
                                        <td><?= e((string) ($columnRow['type'] ?? '')) ?></td>
                                        <td><?= e((string) ($columnRow['max'] ?? '-')) ?></td>
                                        <td><?= e((string) ($columnRow['pass'] ?? '-')) ?></td>
                                        <td><?= e((string) ($columnRow['width'] ?? '-')) ?></td>
                                        <td><?= e((string) ($columnRow['align'] ?? '')) ?></td>
                                        <td><span class="status"><?= !empty($columnRow['visible']) ? 'Shown' : 'Hidden' ?></span></td>
                                        <td><?= e((string) ($columnRow['source'] ?? '')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="marksheet-section-card" id="attendance">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Attendance</p>
                            <h3>Attendance</h3>
                            <p class="report-sheet-note">The builder shows attendance if it is configured. Missing attendance data will fall back to a clean placeholder message.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_attendance" value="1" <?= $checked('show_attendance', 1) ?>>
                            Show Attendance
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_working_days" value="1" <?= $checked('show_working_days', 1) ?>>
                            Show Working Days
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_days_attended" value="1" <?= $checked('show_days_attended', 1) ?>>
                            Show Days Attended
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_attendance_percentage" value="1" <?= $checked('show_attendance_percentage', 1) ?>>
                            Show Attendance Percentage
                        </label>
                    </div>
                </section>

                <section class="marksheet-section-card" id="grading-scale">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Grading</p>
                            <h3>Grading Scale</h3>
                            <p class="report-sheet-note">Use the configured grade rows to preview min and max percentages, points, and remarks.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_grading_scale" value="1" <?= $checked('show_grading_scale', 1) ?>>
                            Show Grading Scale
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_min_percentage" value="1" <?= $checked('show_min_percentage', 1) ?>>
                            Show Minimum Percentage
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_max_percentage" value="1" <?= $checked('show_max_percentage', 1) ?>>
                            Show Maximum Percentage
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_grade_point_scale" value="1" <?= $checked('show_grade_point_scale', 1) ?>>
                            Show Grade Point
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_grade_remark" value="1" <?= $checked('show_grade_remark', 1) ?>>
                            Show Remark
                        </label>
                    </div>
                    <div class="marksheet-column-table-wrap">
                        <table class="marksheet-column-table">
                            <thead>
                                <tr>
                                    <th>Grade</th>
                                    <th>Min %</th>
                                    <th>Max %</th>
                                    <th>Point</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($previewGradeRows): ?>
                                    <?php foreach ($previewGradeRows as $index => $gradeRow): ?>
                                        <?php
                                            $gradeName = trim((string) ($gradeRow['grade_name'] ?? ''));
                                            $gradePoint = (float) ($previewGradePoints[strtoupper($gradeName)] ?? 0);
                                        ?>
                                        <tr>
                                            <td><?= e($gradeName !== '' ? $gradeName : 'Grade ' . ($index + 1)) ?></td>
                                            <td><?= e($formatNumber((float) ($gradeRow['min_mark'] ?? 0))) ?></td>
                                            <td><?= e($formatNumber((float) ($gradeRow['max_mark'] ?? 0))) ?></td>
                                            <td><?= e($formatNumber($gradePoint, 1)) ?></td>
                                            <td><?= e((string) ($gradeRow['remark'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5">No grading scale has been configured.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="marksheet-section-card" id="comments">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Comments</p>
                            <h3>Comments</h3>
                            <p class="report-sheet-note">Use existing teacher comments and settings-based principal comments without inventing data.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_principal_comment" value="1" <?= $checked('show_principal_comment', 1) ?>>
                            Show Principal Comment
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_teacher_comment" value="1" <?= $checked('show_teacher_comment', 1) ?>>
                            Show Teacher Comment
                        </label>
                        <label>
                            <span>Principal Comment Source</span>
                            <input type="text" name="principal_comment_source" value="<?= e($value('principal_comment_source', 'settings')) ?>">
                        </label>
                        <label>
                            <span>Teacher Comment Source</span>
                            <input type="text" name="teacher_comment_source" value="<?= e($value('teacher_comment_source', 'exam_comments')) ?>">
                        </label>
                        <label>
                            <span>Comment Box Height (px)</span>
                            <input type="number" name="comment_box_height" value="<?= e((int) $value('comment_box_height', 84)) ?>">
                        </label>
                    </div>
                </section>

                <section class="marksheet-section-card" id="signatures-stamp">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Signatures</p>
                            <h3>Signatures &amp; Stamp</h3>
                            <p class="report-sheet-note">Upload the signature and stamp images used at the bottom of the report sheet.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_principal_signature" value="1" <?= $checked('show_principal_signature', 1) ?>>
                            Show Principal Signature
                        </label>
                        <label>
                            <span>Principal Signature Width (px)</span>
                            <input type="number" name="principal_signature_width" value="<?= e((int) $value('principal_signature_width', 120)) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_class_teacher_signature" value="1" <?= $checked('show_class_teacher_signature', 1) ?>>
                            Show Class Teacher Signature
                        </label>
                        <label>
                            <span>Class Teacher Signature Width (px)</span>
                            <input type="number" name="class_teacher_signature_width" value="<?= e((int) $value('class_teacher_signature_width', 120)) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_school_stamp" value="1" <?= $checked('show_school_stamp', 1) ?>>
                            Show School Stamp
                        </label>
                        <label>
                            <span>School Stamp Width (px)</span>
                            <input type="number" name="school_stamp_width" value="<?= e((int) $value('school_stamp_width', 120)) ?>">
                        </label>
                        <label class="form-wide">
                            <span>Principal Signature Image</span>
                            <div class="marksheet-upload-card">
                                <?php if (!empty($template['left_signature'])): ?>
                                    <img src="<?= e(public_upload_url((string) $template['left_signature'])) ?>" alt="Principal signature preview">
                                <?php else: ?>
                                    <div class="marksheet-upload-placeholder">No principal signature uploaded</div>
                                <?php endif; ?>
                                <input type="file" name="left_signature" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </label>
                        <label class="form-wide">
                            <span>Class Teacher Signature Image</span>
                            <div class="marksheet-upload-card">
                                <?php if (!empty($template['middle_signature'])): ?>
                                    <img src="<?= e(public_upload_url((string) $template['middle_signature'])) ?>" alt="Class teacher signature preview">
                                <?php else: ?>
                                    <div class="marksheet-upload-placeholder">No class teacher signature uploaded</div>
                                <?php endif; ?>
                                <input type="file" name="middle_signature" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </label>
                        <label class="form-wide">
                            <span>School Stamp Image</span>
                            <div class="marksheet-upload-card">
                                <?php if (!empty($template['right_signature'])): ?>
                                    <img src="<?= e(public_upload_url((string) $template['right_signature'])) ?>" alt="School stamp preview">
                                <?php else: ?>
                                    <div class="marksheet-upload-placeholder">No school stamp uploaded</div>
                                <?php endif; ?>
                                <input type="file" name="right_signature" accept=".jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </label>
                    </div>
                </section>

                <section class="marksheet-section-card" id="footer-settings">
                    <div class="marksheet-section-head">
                        <div>
                            <p class="eyebrow">Footer</p>
                            <h3>Footer &amp; Print Settings</h3>
                            <p class="report-sheet-note">Finish the template with print date, footer note, and editable HTML blocks for the report header and footer.</p>
                        </div>
                    </div>
                    <div class="marksheet-field-grid four-cols">
                        <label class="toggle-field">
                            <input type="checkbox" name="show_print_date" value="1" <?= $checked('show_print_date', 1) ?>>
                            Show Print Date
                        </label>
                        <label>
                            <span>Print Date Format</span>
                            <input type="text" name="print_date_format" value="<?= e($value('print_date_format', 'd.M.Y')) ?>">
                        </label>
                        <label class="toggle-field">
                            <input type="checkbox" name="show_powered_by" value="1" <?= $checked('show_powered_by', 0) ?>>
                            Show Powered By Text
                        </label>
                        <label class="form-wide">
                            <span>Footer Note</span>
                            <textarea name="footer_note" rows="3" placeholder="Optional footer note for the printed report sheet."><?= e($value('footer_note', '')) ?></textarea>
                        </label>
                    </div>

                    <div class="marksheet-content-grid">
                        <div class="rich-editor form-wide" data-rich-editor>
                            <div class="editor-title">
                                <span>Header Content</span>
                                <button type="button" data-editor-toggle>HTML Code</button>
                            </div>
                            <div class="editor-toolbar">
                                <button type="button" data-command="bold"><strong>B</strong></button>
                                <button type="button" data-command="italic"><em>I</em></button>
                                <button type="button" data-command="underline"><u>U</u></button>
                                <select data-command="fontName">
                                    <option value="">Font</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Tahoma">Tahoma</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                                <select data-command="formatBlock">
                                    <option value="">Style</option>
                                    <option value="h2">Heading</option>
                                    <option value="h3">Subheading</option>
                                    <option value="p">Paragraph</option>
                                    <option value="div">Block</option>
                                </select>
                                <input type="color" value="#17394a" data-command="foreColor" title="Text color">
                                <button type="button" data-create-link>Link</button>
                            </div>
                            <div class="editor-surface" contenteditable="true" data-editor-surface><?= $headerContent ?></div>
                            <textarea name="header_content" rows="8" data-editor-source><?= e($headerContent) ?></textarea>
                        </div>

                        <div class="rich-editor form-wide" data-rich-editor>
                            <div class="editor-title">
                                <span>Footer Content</span>
                                <button type="button" data-editor-toggle>HTML Code</button>
                            </div>
                            <div class="editor-toolbar">
                                <button type="button" data-command="bold"><strong>B</strong></button>
                                <button type="button" data-command="italic"><em>I</em></button>
                                <button type="button" data-command="underline"><u>U</u></button>
                                <select data-command="fontName">
                                    <option value="">Font</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Tahoma">Tahoma</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                                <select data-command="formatBlock">
                                    <option value="">Style</option>
                                    <option value="h3">Subheading</option>
                                    <option value="p">Paragraph</option>
                                    <option value="div">Block</option>
                                </select>
                                <input type="color" value="#17394a" data-command="foreColor" title="Text color">
                                <button type="button" data-create-link>Link</button>
                            </div>
                            <div class="editor-surface" contenteditable="true" data-editor-surface><?= $footerContent ?></div>
                            <textarea name="footer_content" rows="6" data-editor-source><?= e($footerContent) ?></textarea>
                        </div>
                    </div>

                    <div class="marksheet-token-bank">
                        <div class="panel-header compact">
                            <div>
                                <p class="eyebrow">Placeholders</p>
                                <h3>Insert Template Tokens</h3>
                            </div>
                        </div>
                        <div class="tag-bank">
                            <?php foreach ($tags as $tag): ?>
                                <button type="button" data-template-tag="<?= e($tag) ?>"><?= e($tag) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <p class="report-sheet-note">Tokens are inserted into the active header or footer editor. Empty values render as blanks instead of fake sample data.</p>
                    </div>
                </section>
            </div>

            <aside class="marksheet-preview-panel panel">
                <div class="marksheet-section-head">
                    <div>
                        <p class="eyebrow">Design Preview</p>
                        <h3>Sample report sheet</h3>
                        <p class="report-sheet-note">Design Preview - sample data only. Use the real result preview for actual student report sheets.</p>
                    </div>
                    <div class="preview-links">
                        <a class="secondary-action" href="/exams/result-preview">Preview With Real Student Result</a>
                    </div>
                </div>

                <div class="marksheet-preview-sheet">
                    <div class="marksheet-preview-header">
                        <div class="marksheet-preview-brand">
                            <?php if ($show('show_school_logo', 1) && $schoolLogoUrl !== ''): ?>
                                <img src="<?= e($schoolLogoUrl) ?>" alt="<?= e($schoolName) ?>">
                            <?php else: ?>
                                <div class="marksheet-preview-logo-placeholder"><?= e($schoolPhotoFallback) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="marksheet-preview-heading">
                            <?php if ($show('show_school_name', 1)): ?><strong><?= e($schoolName) ?></strong><?php endif; ?>
                            <?php if ($show('show_school_address', 1) && $schoolAddress !== ''): ?><span><?= e($schoolAddress) ?></span><?php endif; ?>
                            <small>
                                <?php if ($show('show_school_phone', 1) && $schoolPhone !== ''): ?><?= e($schoolPhone) ?><?php endif; ?>
                                <?php if ($show('show_school_phone', 1) && $schoolPhone !== '' && $show('show_school_email', 1) && $schoolEmail !== ''): ?> | <?php endif; ?>
                                <?php if ($show('show_school_email', 1) && $schoolEmail !== ''): ?><?= e($schoolEmail) ?><?php endif; ?>
                            </small>
                        </div>
                        <div class="marksheet-preview-photo">
                            <?php if ($show('show_student_photo', 1)): ?>
                                <div class="marksheet-preview-photo-placeholder">
                                    <span><?= e($previewPhotoInitials) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="marksheet-preview-title">
                        <strong>MARKSHEET</strong>
                        <span><?= e($value('academic_session', $academicYearFallback)) ?></span>
                    </div>

                    <?php if ($show('show_biodata_section', 1)): ?>
                        <table class="marksheet-preview-biodata">
                            <tbody>
                                <tr>
                                    <td>Student Name</td>
                                    <td><?= e($previewStudent['student_name']) ?></td>
                                    <td>Father's Name</td>
                                    <td><?= e($previewStudent['father_name']) ?></td>
                                </tr>
                                <tr>
                                    <td>Roll No.</td>
                                    <td><?= e($previewStudent['roll_number']) ?></td>
                                    <td>Date of Birth</td>
                                    <td><?= e($previewStudent['date_of_birth']) ?></td>
                                </tr>
                                <tr>
                                    <td>Class</td>
                                    <td><?= e($previewStudent['class']) ?></td>
                                    <td>Admission Date</td>
                                    <td><?= e($previewStudent['admission_date']) ?></td>
                                </tr>
                                <tr>
                                    <td>Admission No.</td>
                                    <td><?= e($previewStudent['register_no']) ?></td>
                                    <td>Gender</td>
                                    <td><?= e($previewStudent['gender']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if ($show('show_subject_table', 1)): ?>
                        <div class="marksheet-preview-table-wrap">
                            <table class="marksheet-preview-table">
                                <thead>
                                    <tr>
                                        <?php if ($show('show_subject_name', 1)): ?><th class="is-subject">Subject</th><?php endif; ?>
                                        <?php if ($show('show_dynamic_mark_distribution_columns', 1)): ?>
                                            <?php foreach ($previewDistributions as $distribution): ?>
                                                <th class="is-center">
                                                    <?= e((string) ($distribution['name'] ?? '')) ?>
                                                    <br><small>(<?= e($formatNumber((float) ($distribution['max_mark'] ?? 0))) ?>)</small>
                                                </th>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if ($show('show_total', 1)): ?><th>Total</th><?php endif; ?>
                                        <?php if ($show('show_grade', 1)): ?><th>Grade</th><?php endif; ?>
                                        <?php if ($show('show_grade_point', 1)): ?><th>Point</th><?php endif; ?>
                                        <?php if ($show('show_remark', 1)): ?><th class="is-remark">Remark</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($previewSubjectRows as $subjectRow): ?>
                                        <tr>
                                            <?php if ($show('show_subject_name', 1)): ?><td class="is-subject"><?= e((string) ($subjectRow['subject'] ?? '')) ?></td><?php endif; ?>
                                            <?php if ($show('show_dynamic_mark_distribution_columns', 1)): ?>
                                                <?php foreach ($subjectRow['cells'] as $cell): ?>
                                                    <td class="is-center"><?= e($formatNumber((float) ($cell['score'] ?? 0), 0)) ?>/<?= e($formatNumber((float) ($cell['max'] ?? 0), ((float) ($cell['max'] ?? 0) == floor((float) ($cell['max'] ?? 0))) ? 0 : 1)) ?></td>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <?php if ($show('show_total', 1)): ?><td><?= e($formatNumber((float) ($subjectRow['total'] ?? 0), 0)) ?></td><?php endif; ?>
                                            <?php if ($show('show_grade', 1)): ?><td><?= e((string) ($subjectRow['grade'] ?? '-')) ?></td><?php endif; ?>
                                            <?php if ($show('show_grade_point', 1)): ?><td><?= e($formatNumber((float) ($subjectRow['point'] ?? 0), 1)) ?></td><?php endif; ?>
                                            <?php if ($show('show_remark', 1)): ?><td class="is-remark"><?= e((string) ($subjectRow['remark'] ?? '-')) ?></td><?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="marksheet-preview-empty">Subject table hidden by template settings.</div>
                    <?php endif; ?>

                    <div class="marksheet-preview-summary-grid">
                        <?php if ($show('show_grand_total', 1)): ?>
                            <div class="marksheet-preview-summary">
                                <span>Grand Total</span>
                                <strong><?= e($formatNumber($previewGrandTotal, 0)) ?>/<?= e($formatNumber($previewObtainable, 0)) ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if ($show('show_grand_total_in_words', 1)): ?>
                            <div class="marksheet-preview-summary">
                                <span>Grand Total in Words</span>
                                <strong><?= e($previewGrandTotalWords) ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if ($show('show_average', 1)): ?>
                            <div class="marksheet-preview-summary">
                                <span>Average</span>
                                <strong><?= e($formatNumber($previewAverage, 2)) ?>%</strong>
                            </div>
                        <?php endif; ?>
                        <?php if ($show('show_gpa', 1)): ?>
                            <div class="marksheet-preview-summary">
                                <span>GPA</span>
                                <strong><?= e($formatNumber($previewGpa, 2)) ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if ($show('show_result_status', 1)): ?>
                            <div class="marksheet-preview-summary">
                                <span>Result Status</span>
                                <strong><?= e($previewResultStatus) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="marksheet-preview-split">
                        <div class="marksheet-preview-box">
                            <h4>Attendance</h4>
                            <?php if ($attendanceAvailable): ?>
                                <dl>
                                    <?php if ($show('show_working_days', 1)): ?><div><dt>Total Working Days</dt><dd><?= e((string) $previewAttendanceWorkingDays) ?></dd></div><?php endif; ?>
                                    <?php if ($show('show_days_attended', 1)): ?><div><dt>Days Present</dt><dd><?= e((string) $previewAttendanceAttended) ?></dd></div><?php endif; ?>
                                    <?php if ($show('show_attendance_percentage', 1)): ?><div><dt>Attendance %</dt><dd><?= e($formatNumber((float) $previewAttendancePercentage, 2)) ?>%</dd></div><?php endif; ?>
                                </dl>
                            <?php else: ?>
                                <p>Attendance not available.</p>
                            <?php endif; ?>
                        </div>

                        <div class="marksheet-preview-box">
                            <h4>Grading Scale</h4>
                            <?php if ($show('show_grading_scale', 1)): ?>
                                <table class="marksheet-preview-grade-table">
                                    <tbody>
                                        <?php foreach ($previewGradeRows as $gradeRow): ?>
                                            <tr>
                                                <td><?= e((string) ($gradeRow['grade_name'] ?? '')) ?></td>
                                                <?php if ($show('show_min_percentage', 1)): ?><td><?= e($formatNumber((float) ($gradeRow['min_mark'] ?? 0))) ?></td><?php endif; ?>
                                                <?php if ($show('show_max_percentage', 1)): ?><td><?= e($formatNumber((float) ($gradeRow['max_mark'] ?? 0))) ?></td><?php endif; ?>
                                                <?php if ($show('show_grade_point_scale', 1)): ?><td><?= e($formatNumber((float) ($previewGradePoints[strtoupper(trim((string) ($gradeRow['grade_name'] ?? '')))] ?? 0), 1)) ?></td><?php endif; ?>
                                                <?php if ($show('show_grade_remark', 1)): ?><td><?= e((string) ($gradeRow['remark'] ?? '')) ?></td><?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>Grading scale hidden by template settings.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="marksheet-preview-comments">
                        <?php if ($show('show_principal_comment', 1)): ?>
                            <div class="marksheet-preview-box">
                                <h4>Principal Comment</h4>
                                <p><?= e($previewPrincipalComment !== '' ? $previewPrincipalComment : 'No principal comment configured yet.') ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if ($show('show_teacher_comment', 1)): ?>
                            <div class="marksheet-preview-box">
                                <h4>Teacher Comment</h4>
                                <p><?= e($previewTeacherComment !== '' ? $previewTeacherComment : 'No teacher comment available yet.') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="marksheet-preview-signatures">
                        <?php if ($show('show_principal_signature', 1)): ?>
                            <figure>
                                <?php if ($previewPrincipalSignature !== ''): ?>
                                    <img src="<?= e(public_upload_url($previewPrincipalSignature)) ?>" alt="Principal signature preview">
                                <?php else: ?>
                                    <div class="marksheet-preview-signature-placeholder"></div>
                                <?php endif; ?>
                                <figcaption>Principal</figcaption>
                            </figure>
                        <?php endif; ?>

                        <?php if ($show('show_class_teacher_signature', 1)): ?>
                            <figure>
                                <?php if ($previewTeacherSignature !== ''): ?>
                                    <img src="<?= e(public_upload_url($previewTeacherSignature)) ?>" alt="Class teacher signature preview">
                                <?php else: ?>
                                    <div class="marksheet-preview-signature-placeholder"></div>
                                <?php endif; ?>
                                <figcaption>Class Teacher</figcaption>
                            </figure>
                        <?php endif; ?>

                        <?php if ($show('show_school_stamp', 1)): ?>
                            <figure>
                                <?php if ($previewStamp !== ''): ?>
                                    <img src="<?= e(public_upload_url($previewStamp)) ?>" alt="School stamp preview">
                                <?php else: ?>
                                    <div class="marksheet-preview-stamp-placeholder">STAMP</div>
                                <?php endif; ?>
                                <figcaption>School Stamp</figcaption>
                            </figure>
                        <?php endif; ?>
                    </div>

                    <?php if ($show('show_print_date', 1) || $value('footer_note', '') !== ''): ?>
                        <div class="marksheet-preview-footer">
                            <?php if ($show('show_print_date', 1)): ?><span>Print Date: <?= e(date($value('print_date_format', 'd.M.Y'))) ?></span><?php endif; ?>
                            <?php if ($value('footer_note', '') !== ''): ?><span><?= e($value('footer_note', '')) ?></span><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </form>

    <?php if (can('exams', 'edit')): ?>
        <div class="marksheet-sticky-savebar panel">
            <div class="marksheet-sticky-copy">
                <strong>Review your template settings before saving.</strong>
                <span><?= $templateId > 0 ? 'Last edited just now' : 'Save the template to make the preview and default actions available.' ?></span>
            </div>
            <div class="marksheet-sticky-actions">
                <a class="secondary-action" href="/exams/marksheet-templates">Cancel</a>
                <?php if ($templateId > 0): ?>
                    <a class="secondary-action" href="/exams/marksheet-templates/preview?id=<?= e((string) $templateId) ?>">Preview Report</a>
                <?php endif; ?>
                <button type="submit" form="marksheet-template-form" class="primary-action">Save Changes</button>
            </div>
        </div>
    <?php endif; ?>

    <section class="panel marksheet-template-list-panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Templates</p>
                <h3>Template List</h3>
            </div>
        </div>
        <div class="table-wrap marksheet-template-table-wrap">
            <table class="marksheet-template-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Default</th>
                        <th>Layout</th>
                        <th>Photo</th>
                        <th>Attendance</th>
                        <th>Grades</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $templateRow): ?>
                        <?php
                            $templateRowId = (int) ($templateRow['id'] ?? 0);
                            $createdAt = $templateTimestamp($templateRow);
                            $templateSession = trim((string) ($templateRow['academic_session'] ?? ''));
                        ?>
                        <tr>
                            <td><?= e((string) ($templateRow['name'] ?? 'Default Report Sheet')) ?></td>
                            <td><?= e($templateSession !== '' ? $templateSession : '-') ?></td>
                            <td><?= e(trim((string) ($templateRow['status'] ?? 'Active')) ?: 'Active') ?></td>
                            <td><span class="status"><?= (int) ($templateRow['is_default'] ?? 0) === 1 ? 'Default' : 'Standard' ?></span></td>
                            <td><?= e(($templateRow['paper_size'] ?? 'A4') . ' / ' . ($templateRow['page_layout'] ?? 'Portrait')) ?></td>
                            <td><?= e(($templateRow['photo_style'] ?? 'Square') . ' / ' . ((int) ($templateRow['photo_size'] ?? 100)) . 'px') ?></td>
                            <td><span class="status"><?= (int) ($templateRow['attendance_percentage'] ?? 0) === 1 ? 'Shown' : 'Hidden' ?></span></td>
                            <td><span class="status"><?= (int) ($templateRow['grading_scale'] ?? 0) === 1 ? 'Shown' : 'Hidden' ?></span></td>
                            <td><?= $formatTemplateDate($createdAt) ?></td>
                            <td class="row-actions">
                                <?php if ($templateRowId > 0): ?>
                                    <a href="/exams/marksheet-templates/preview?id=<?= e((string) $templateRowId) ?>">View</a>
                                    <?php if (can('exams', 'edit')): ?>
                                        <a href="/exams/marksheet-templates?edit=<?= e((string) $templateRowId) ?>">Edit</a>
                                        <?php if ((int) ($templateRow['is_default'] ?? 0) !== 1): ?>
                                            <form method="post" action="/exams/marksheet-templates/default">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $templateRowId) ?>">
                                                <button type="submit">Set Default</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (can('exams', 'delete')): ?>
                                        <form method="post" action="/exams/marksheet-templates/delete">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= e((string) $templateRowId) ?>">
                                            <button type="submit">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($templates)): ?>
                        <tr><td colspan="10">No marksheet template has been created yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
