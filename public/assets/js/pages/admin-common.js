(function(){
    var selectAll = document.getElementById('select-all');
    var applyBtn = document.getElementById('bulk-apply');
    var form = document.getElementById('bulk-action-form');

    function updateApply() {
        var any = Array.from(document.querySelectorAll('.row-checkbox')).some(function(cb){return cb.checked;});
        if(applyBtn) applyBtn.disabled = !any;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function(){
            var checked = this.checked;
            document.querySelectorAll('.row-checkbox').forEach(function(cb){ cb.checked = checked; });
            updateApply();
        });
    }

    document.addEventListener('change', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('row-checkbox')) {
            updateApply();
        }
    });

    if (form) {
        form.addEventListener('submit', function(e){
            var action = document.getElementById('bulk-action-select').value;
            if (!action) { 
                e.preventDefault(); 
                alert('Please select an action first.'); 
                return false; 
            }
            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete the selected items?')) { 
                    e.preventDefault(); 
                    return false; 
                }
            }
        });
    }
})();

(function(){
    var searchInputs = document.querySelectorAll('.filter-bar .filter-search input[type="text"]');
    if (!searchInputs.length) return;

    function debounce(fn, delay) {
        var timer;
        return function() {
            var args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function(){ fn.apply(null, args); }, delay);
        };
    }

    function shouldSubmit(value, lastValue) {
        if (value === lastValue) return false;
        if (value === '') return true;
        return value.length >= 3;
    }

    searchInputs.forEach(function(input){
        var lastSubmitted = input.value || '';
        var handler = debounce(function(){
            var value = input.value.trim();
            if (!shouldSubmit(value, lastSubmitted)) return;

            lastSubmitted = value;
            var form = input.closest('form');
            if (!form) return;
            form.submit();
        }, 700);

        input.addEventListener('input', handler);
        input.addEventListener('change', function(){
            var value = input.value.trim();
            if (!shouldSubmit(value, lastSubmitted)) return;

            lastSubmitted = value;
            var form = input.closest('form');
            if (!form) return;
            form.submit();
        });
    });
})();