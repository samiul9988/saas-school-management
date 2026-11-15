<script>
    /**
     * Hides every <option> in a target <select> whose data-class-id doesn't match
     * the chosen Class, so e.g. a Section dropdown only offers that class's
     * sections. All target options must already be rendered in the DOM with a
     * data-class-id attribute; the placeholder (value="") is always kept visible.
     *
     * @param {string} classSelectId  id of the Class <select>
     * @param {string} targetSelectId id of the <select> to filter
     */
    window.wireClassFilteredSelect = function (classSelectId, targetSelectId) {
        var classSelect = document.getElementById(classSelectId);
        var targetSelect = document.getElementById(targetSelectId);

        if (! classSelect || ! targetSelect) {
            return;
        }

        function apply() {
            var classId = classSelect.value;

            Array.from(targetSelect.options).forEach(function (option) {
                if (! option.value) {
                    return;
                }

                option.hidden = classId !== '' && option.dataset.classId !== classId;
            });

            var selected = targetSelect.options[targetSelect.selectedIndex];
            if (selected && selected.hidden) {
                targetSelect.value = '';
            }
        }

        classSelect.addEventListener('change', apply);
        apply();
    };
</script>
