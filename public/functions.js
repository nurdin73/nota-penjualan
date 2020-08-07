function check() {  
    $('#dataTable').on('change', 'tbody tr td .check',function (e) {  
        e.preventDefault()
        var checked = $(this).is(':checked');
        var checkedData = $('input:checkbox:checked').length
        if(checked) {
            var parents = $($(this).parent().parent())
            parents.css('background-color', '#ccc')
        } else {
            var parents = $($(this).parent().parent())
            parents.css('background-color', '#fff')
        }

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