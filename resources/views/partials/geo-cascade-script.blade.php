<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectCityLabel = @json(__('shipments.select_city'));

        document.querySelectorAll('[data-geo-cascade]').forEach((root) => {
            const url = root.dataset.citiesUrl;
            const dept = root.querySelector('[data-role="department"]');
            const city = root.querySelector('[data-role="city"]');

            async function loadCities() {
                const departmentId = dept.value;
                city.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = selectCityLabel;
                city.appendChild(placeholder);

                if (!departmentId) {
                    return;
                }

                const res = await fetch(
                    url + '?department_id=' + encodeURIComponent(departmentId),
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );
                const rows = await res.json();

                rows.forEach((row) => {
                    const opt = document.createElement('option');
                    opt.value = row.id;
                    opt.textContent = row.name;
                    city.appendChild(opt);
                });

                const oldCity = root.dataset.oldCity;
                if (oldCity) {
                    city.value = oldCity;
                }
            }

            dept.addEventListener('change', () => {
                root.dataset.oldCity = '';
                loadCities();
            });

            if (dept.value) {
                loadCities();
            }
        });
    });
</script>
