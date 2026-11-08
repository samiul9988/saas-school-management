<script>
    window.wireDynamicSubjectSelect = function (sectionSelectId, examSelectId, groupSelectId, subjectSelectId, mode, selectedSubjectId) {
        var sectionSelect = document.getElementById(sectionSelectId);
        var examSelect = document.getElementById(examSelectId);
        var groupSelect = document.getElementById(groupSelectId);
        var subjectSelect = document.getElementById(subjectSelectId);
        if (! sectionSelect || ! examSelect || ! subjectSelect) { return; }
        var placeholderOption = subjectSelect.options.length ? subjectSelect.options[0].cloneNode(true) : null;
        var base = '{{ route('school.mark-input.subjects') }}';

        function loadSubjects(preserve) {
            var sectionId = sectionSelect.value;
            var examId = examSelect.value;
            if (! sectionId || ! examId) { return; }

            var params = new URLSearchParams({ section_id: sectionId, exam_id: examId, mode: mode });
            if (groupSelect && groupSelect.value) { params.set('group_name', groupSelect.value); }

            fetch(base + '?' + params.toString(), { headers: { Accept: 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    subjectSelect.innerHTML = '';
                    if (placeholderOption) { subjectSelect.appendChild(placeholderOption.cloneNode(true)); }
                    (data.subjects || []).forEach(function (subject) {
                        var option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.name;
                        if (preserve && String(subject.id) === String(preserve)) { option.selected = true; }
                        subjectSelect.appendChild(option);
                    });
                })
                .catch(function () {});
        }

        sectionSelect.addEventListener('change', function () { loadSubjects(null); });
        examSelect.addEventListener('change', function () { loadSubjects(null); });
        if (groupSelect) { groupSelect.addEventListener('change', function () { loadSubjects(null); }); }
        if (sectionSelect.value && examSelect.value) { loadSubjects(selectedSubjectId || null); }
    };
</script>
