    <div class="sheet">
        <table class="head-table w-full">
            <tr>
                <td class="logo-cell">
                    <div class="logo">
                        @if ($school->logo ?? false)
                            <img src="{{ ($forPdf ?? false) ? public_path('storage/'.$school->logo) : asset('storage/'.$school->logo) }}">
                        @endif
                    </div>
                </td>
                <td>
                    <div class="school-name">{{ $school->name }}</div>
                    <div class="school-addr">{{ $school->address }}</div>
                    <div class="transcript-title">
                        <span class="transcript-box">{{ $variant == 4 ? 'ACADEMIC PROGRESS REPORT' : 'ACADEMIC TRANSCRIPT' }}</span>
                    </div>
                    @if ($variant == 4)
                        <div class="school-addr" style="margin-top:2px;">Special Evaluation - {{ $yearSession }}</div>
                    @endif
                </td>
                <td style="width: 150px;">
                    <table class="legend-table w-full">
                        <tr><th>Range</th><th>Grade</th><th>GP</th></tr>
                        @foreach ($grades as $grade)
                            <tr>
                                <td>{{ rtrim(rtrim(number_format($grade->min_percent, 0)), '.') }}-{{ rtrim(rtrim(number_format($grade->max_percent, 0)), '.') }}</td>
                                <td>{{ $grade->name }}</td>
                                <td>{{ rtrim(rtrim(number_format($grade->grade_point, 2), '0'), '.') ?: '0' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

        <table class="photo-table">
            <tr>
                <td style="width: 50px;">
                    <div class="student-photo">
                        @if ($student['photo'] ?? false)
                            <img src="{{ $student['photo'] }}">
                        @endif
                    </div>
                </td>
                <td>
                    <table class="info-table">
                        <tr>
                            <td class="label">Name of Student</td><td class="sep">:</td><td>{{ $student['name'] }}</td>
                            <td class="label">Exam</td><td class="sep">:</td><td>{{ $examName }}</td>
                        </tr>
                        <tr>
                            <td class="label">Father's Name</td><td class="sep">:</td><td>{{ $student['father_name'] ?? '-' }}</td>
                            <td class="label">Year/Session</td><td class="sep">:</td><td>{{ $yearSession }}</td>
                        </tr>
                        <tr>
                            <td class="label">Mother's Name</td><td class="sep">:</td><td>{{ $student['mother_name'] ?? '-' }}</td>
                            <td class="label">Group</td><td class="sep">:</td><td>{{ $student['group_name'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Student ID</td><td class="sep">:</td><td>{{ $student['id'] }}</td>
                            @if ($variant == 4)
                                <td class="label">Section</td><td class="sep">:</td><td>{{ $student['section'] }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="label">Roll No.</td><td class="sep">:</td><td>{{ $student['roll'] }}</td>
                            @if ($variant == 4)
                                <td class="label">Contact</td><td class="sep">:</td><td>{{ $student['contact'] ?? '-' }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="label">Class</td><td class="sep">:</td><td>{{ $student['class'] }}{{ $variant != 4 ? ' - '.$student['section'] : '' }}</td>
                            @if ($variant == 4)
                                <td class="label">DOB</td><td class="sep">:</td><td>{{ $student['dob'] ?? '-' }}</td>
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="marks-table">
            <thead>
                <tr>
                    <th rowspan="2" class="subject-name">Name of Subjects</th>
                    <th rowspan="2">Full<br>Marks</th>
                    <th rowspan="2">Highest<br>Marks</th>
                    <th colspan="{{ max(count($subjectRows[0]['components'] ?? []), 1) }}">Obtaining Marks</th>
                    <th rowspan="2">Total<br>Marks</th>
                    <th rowspan="2">Letter<br>Grade</th>
                    <th rowspan="2">Grade<br>Point</th>
                </tr>
                <tr>
                    @forelse (($subjectRows[0]['components'] ?? []) as $component)
                        <th>{{ $component['label'] }}</th>
                    @empty
                        <th>Obtained</th>
                    @endforelse
                </tr>
            </thead>
            <tbody>
                @foreach ($subjectRows as $row)
                    <tr>
                        <td class="subject-name">{{ $row['name'] }}</td>
                        <td>{{ $row['full'] }}</td>
                        <td>{{ $row['highest'] }}</td>
                        @forelse ($row['components'] as $component)
                            <td>{{ $component['value'] }}</td>
                        @empty
                            <td>{{ $row['total'] }}</td>
                        @endforelse
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['letterGrade'] }}</td>
                        <td>{{ $row['gradePoint'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="subject-name">Total Exam Marks</td>
                    <td>{{ $totals['full'] }}</td>
                    <td></td>
                    <td colspan="{{ max(count($subjectRows[0]['components'] ?? []), 1) }}"></td>
                    <td>{{ $totals['obtained'] }}</td>
                    <td>{{ $totals['letterGrade'] }}</td>
                    <td>{{ $totals['gpa'] }}</td>
                </tr>
            </tfoot>
        </table>

        @if ($variant == 2)
            <table class="status-table">
                <tr><td class="k">Result Status</td><td>{{ $totals['resultStatus'] }}</td><td class="k">Class Position</td><td>{{ $meta['classPosition'] ?? '-' }}</td></tr>
                <tr><td class="k">GPA (Without 4th)</td><td>{{ $totals['gpa'] }}</td><td class="k">Shift Position</td><td>{{ $meta['shiftPosition'] ?? '-' }}</td></tr>
                <tr><td class="k">Failed Subject(s)</td><td>{{ $meta['failedSubjects'] ?? 0 }}</td><td class="k">Section Position</td><td>{{ $meta['sectionPosition'] ?? '-' }}</td></tr>
            </table>
        @elseif ($variant == 4)
            <table class="status-table">
                <tr><td class="k">Total Students in this Section</td><td>{{ $meta['totalStudents'] ?? '-' }}</td><td class="k">Result Status</td><td>{{ $totals['resultStatus'] }}</td></tr>
                <tr><td class="k">Working Days (Classes were held)</td><td>{{ $meta['workingDays'] ?? '-' }}</td><td class="k">Number of Failed Subject(s)</td><td>{{ $meta['failedSubjects'] ?? 0 }}</td></tr>
                <tr><td class="k">Student's Attendance</td><td>{{ $meta['totalPresent'] ?? '-' }}</td><td class="k">Class Position</td><td>{{ $meta['classPosition'] ?? '-' }}</td></tr>
            </table>
            <div style="border: 1px solid #333; margin-top: 4px; padding: 10px; font-size: 7.5px;">Comments</div>
        @else
            <table class="status-table">
                <tr><td class="k">Result Status</td><td>{{ $totals['resultStatus'] }}</td><td class="k">Mind &amp; Behavior Evaluation</td><td>Excellent</td></tr>
                <tr><td class="k">GPA (Without 4th)</td><td>{{ $totals['gpa'] }}</td><td class="k"></td><td>Good</td></tr>
                <tr><td class="k">Failed Subject(s)</td><td>{{ $meta['failedSubjects'] ?? 0 }}</td><td class="k"></td><td>Average</td></tr>
                <tr><td class="k">Working Days</td><td>{{ $meta['workingDays'] ?? '-' }}</td><td class="k"></td><td>Poor</td></tr>
                <tr><td class="k">Total Present</td><td>{{ $meta['totalPresent'] ?? '-' }}</td><td class="k">Co-Curricular Activities</td><td>Sports</td></tr>
                <tr><td class="k">Total Absent</td><td>{{ $meta['totalAbsent'] ?? '-' }}</td><td class="k"></td><td>Cultural Function / Scout / BNCC / Math Olympiad</td></tr>
            </table>
        @endif

        @if ($qrDataUri ?? false)
            <div class="qr-box"><img src="{{ $qrDataUri }}"></div>
        @endif

        <table class="sign-table">
            <tr>
                <td>Signature of Section Teacher</td>
                <td>Signature of Headmaster</td>
            </tr>
        </table>

        <div class="footer-row">
            <div class="left">SaaS School Management</div>
            <div class="right">Date of Publication of Results: {{ $publishDate }}</div>
        </div>
    </div>
