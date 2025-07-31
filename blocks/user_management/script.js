document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('ajax-page-link')) {
            e.preventDefault();
            const page = e.target.getAttribute('data-page');

            fetch(M.cfg.wwwroot + '/blocks/user_management/ajax.php?page=' + page + '&sesskey=' + M.cfg.sesskey)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    const container = document.getElementById('usertable');
                    if (container) {
                        container.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                });
        }
    });
});
