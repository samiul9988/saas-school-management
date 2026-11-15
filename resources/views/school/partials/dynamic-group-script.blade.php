<script>
    /**
     * Repopulates a Group <select> with the group names that actually exist
     * for the chosen Section/Class, instead of a fixed list, whenever it changes.
     *
     * @param {string} scopeSelectId  id of the Section or Class <select>
     * @param {string} groupSelectId id of the Group <select> to repopulate
     * @param {string} scope         'sections', 'classes', or 'admission-classes'
     * @param {string|null} selectedGroup group to keep selected on initial load
     * @param {string|null} idAttr   when set, the scope id is read from the selected
     *                               <option>'s data-{idAttr} attribute instead of its value
     *                               (e.g. a Section select filtering by its class)
     */
    window.wireDynamicGroupSelect = function (scopeSelectId, groupSelectId, scope, selectedGroup, idAttr) {
        var scopeSelect = document.getElementById(scopeSelectId);
        var groupSelect = document.getElementById(groupSelectId);

        if (! scopeSelect || ! groupSelect) {
            return;
        }

        var placeholderOption = groupSelect.options.length ? groupSelect.options[0].cloneNode(true) : null;
        var urlTemplates = {
            sections: {!! json_encode(route('school.sections.groups', ['section' => '__ID__'])) !!},
            classes: {!! json_encode(route('school.classes.groups', ['class' => '__ID__'])) !!},
            'admission-classes': {!! json_encode(route('school.classes.admission-groups', ['class' => '__ID__'])) !!},
        };

        function resolveScopeId() {
            if (! idAttr) {
                return scopeSelect.value;
            }

            var option = scopeSelect.options[scopeSelect.selectedIndex];

            return option ? option.dataset[idAttr] : null;
        }

        function loadGroups(preserve) {
            var scopeId = resolveScopeId();

            if (! scopeId) {
                return;
            }

            var url = urlTemplates[scope].replace('__ID__', scopeId);

            fetch(url, { headers: { Accept: 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    groupSelect.innerHTML = '';

                    if (placeholderOption) {
                        groupSelect.appendChild(placeholderOption.cloneNode(true));
                    }

                    (data.groups || []).forEach(function (group) {
                        var option = document.createElement('option');
                        option.value = group;
                        option.textContent = group;

                        if (preserve && group === preserve) {
                            option.selected = true;
                        }

                        groupSelect.appendChild(option);
                    });
                })
                .catch(function () {});
        }

        scopeSelect.addEventListener('change', function () { loadGroups(null); });

        if (scopeSelect.value) {
            loadGroups(selectedGroup || null);
        }
    };
</script>
