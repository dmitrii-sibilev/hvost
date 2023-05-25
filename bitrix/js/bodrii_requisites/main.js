BX.ready(function () {
    let menu_cont = document.getElementById('top_menu_id_timeman');
    if (menu_cont) {
        let newElem = document.createElement('div');
        newElem.classList.add('main-buttons-item')
        newElem.innerHTML = '<a class="main-buttons-item-link" href="/timeman/new_page.php">' +
            '' +
            '<span class="main-buttons-item-icon"></span><span class="main-buttons-item-text">' +
            '<span class="main-buttons-item-edit-button"></span>' +
            '<span class="main-buttons-item-text-title">НОВАЯ</span>' +
            '<span class="main-buttons-item-drag-button"></span>' +
            '<span class="main-buttons-item-text-marker"></span>' +
            '</span><span class="main-buttons-item-counter"></span>' +
            '</a>' +
            ''
        menu_cont.append(newElem);

    }
})