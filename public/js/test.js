// ここから検索非同期処理の記述
$(function () {
    deleteEvent();
    $('#search-btn').on('click', function (e) {
        e.preventDefault();
        let formData = $('#search-form').serialize();
        console.log('検索非同期開始');


        $.ajax({
                url: 'products',
                type: 'GET',
                data: formData,
                dataType: 'html',
            })
            .done(function (data) {
                console.log('検索通信成功');
                console.log(data);
                let newTable = $(data).find('#product-table');
                $('#product-table').html(newTable);
                deleteEvent();

            })
            .fail(function () {
                alert('通信失敗');
            });
    });
});





// ここから削除非同期処理の記述
function deleteEvent() {
    $('.btn-danger').on('click', function (e) {
        e.preventDefault();
        var deleteConfirm = confirm('削除してよろしいでしょうか？');
        if (deleteConfirm == true) {
            console.log('削除非同期開始');
            var clickEle = $(this)
            var product = clickEle.attr('data-product_id');
            var deleteTarget = clickEle.closest('tr');

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: 'destroy',
                    dataType: 'json',
                    data: {
                        'product': product
                    },
                })

                .done(function () {
                    console.log('削除通信成功');
                    deleteTarget.remove();
                })
                .fail(function () {
                    alert('エラー');
                });

            //”削除しても良いですか”のメッセージで”いいえ”を選択すると次に進み処理がキャンセルされます
        } else {
            (function (e) {
                e.preventDefault()
            });
        };
    });
};
