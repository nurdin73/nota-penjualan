Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

function check() {  
    $('#dataTable').on('change', 'tbody tr td .check',function (e) {  
        e.preventDefault()
        // $('#countItems').html(items.length)
        var checked = $(this).is(':checked');
        var checkedData = $('input:checkbox:checked').length
        var array = JSON.parse(localStorage.getItem('listMember'));
        const memberId = $(this).data('id');
        if(checked) {
            $('.floating').fadeIn()
            var parents = $($(this).parent().parent())
            parents.css('background-color', '#ccc') 
            const found = array.find(x => x == memberId);
            if(!found) {
                array.push(memberId)
                localStorage.setItem('listMember', JSON.stringify(array));
            }
        } else {
            var parents = $($(this).parent().parent())
            parents.css('background-color', '#fff')
            array.remove(memberId)
            localStorage.setItem('listMember', JSON.stringify(array));
        }
        if(array.length > 0) {
            $('.floating').fadeIn();
        } else {
            $('.floating').fadeOut()
        }
        const countItem = array.length > 0 ? $('#countItems').html(array.length) : $('#countItems').html(0)
        const countCeck = checkedData > 0 ? $('.count').html(`( ${checkedData} )`) : $('.count').html('( 0 )')
    })
}

// pesan untuk aksi menggunakan sweetalert
function alertMessage(type = '', {icon = 'success', title = '', text = ''}, data = null) {
    if(type == "alert") {
        Swal.fire({
            title: title,
            text: text,
            icon: icon
        })
    } else if("questions") {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then((result) => {
            if(result.value) {

            }
        })
    }
}
validateFile()
function validateFile() {  
    $('#file_excel').change(function (e) {  
        e.preventDefault()
        var fileType = ['.xlsx', '.xls']
        var val = $(this).val()
        if (val.length > 0) {
            var fileValid = false
            for (let i = 0; i < fileType.length; i++) {
                const element = fileType[i];
                if(val.substr(val.length - element.length, element.length).toLowerCase() == element.toLowerCase()) {
                    fileValid = true
                    $('.custom-file-label').text(e.target.files[0].name)
                    break
                }
            }
    
            if(!fileValid) {
                const options = {
                    icon : 'error',
                    text : "extension file harus bertipe " + fileType.join(', '),
                    title : 'Opps!'
                }
                alertMessage("alert", options)
                $('.custom-file-label').text("Choose file excel (.xls, xlsx)")
                $(this).val("")
                return false
            }
        }
        return true
    })
}

function countItem() {  
    const items = JSON.parse(localStorage.getItem('listMember'))
    if(items.length > 0) {
        $('.floating').fadeIn();
        $('#countItems').html(items.length)
    } else {
        $('.check').prop('checked', false)
        $('.check').parent().parent().css('background-color', '#fff')
        $('.floating').fadeOut()
    }
}
setInterval(() => {
    countItem()
}, 500);

$('.fieldData').on('click', '.hapus', function(e) {
    e.preventDefault()
    const memberID = $(this).data('id')
    const itemList = JSON.parse(localStorage.getItem('listMember'))
    const found = itemList.find(x => x == memberID);
    if(!found) {
        console.log('ga ada');
    } else {
        itemList.remove(memberID)
        localStorage.setItem('listMember', JSON.stringify(itemList));
    }
})