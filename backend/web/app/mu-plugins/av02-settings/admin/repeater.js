jQuery(document).ready(function($) {
    $(document).on('click', '.add-item', function() {
        const wrapper = $(this).prev('.hwn-repeater-wrapper');
        const name = wrapper.data('name');
        wrapper.append(
            `<div class="repeater-item">
                <input type="text" name="${name}" value="" />
                <button type="button" class="button remove-item">✕</button>
            </div>`
        );
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('.repeater-item').remove();
    });
});