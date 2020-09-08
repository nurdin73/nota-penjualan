
$(document).ready(function () {
    $('#dataTable').DataTable({
        "serverSide" : true,
        "prosessing" : true,
        "deferRender": true,
        "stateSave": true, 
        "ajax" : URL_LIST.getAllData,
        "columns" : [
            {data : 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            {data : 'member_id', name: 'member_id'},
            {data : 'no_nota', name: 'no_nota'},
            {data : 'total_items', name: 'total_items'},
            {data : 'total', name: 'total'},
            {data : 'action', name: 'action', orderable: false, searchable: false},
        ]
    })
    check()
    validateFile()
});

$('button.tambah').on('click', function (e) {
    e.preventDefault()
    var templateItem = `
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="nama_barang">Nama Item</label>
                    <input type="text" name="nama_barang[]" class="form-control" required>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="qyt">Quantity</label>
                    <input type="number" name="qyt[]" class="form-control" required>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="nilai">Nilai</label>
                    <input type="number" name="nilai[]" class="form-control" required>
                </div>
            </div>
        </div>
    `
    // $('.field-list').html(template + templateItem);
    $(this).before(templateItem)
})

$('.field-list').on('click', '.tambah-update', function (e) {
    e.preventDefault()
    var templateItem = `
        <div class="row">
            <div class="col-sm-6">
            <input type="hidden" name="id[]" value="">
                <div class="form-group">
                    <label for="nama_barang">Nama Item</label>
                    <input type="text" name="nama_barang[]" class="form-control" required>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="qyt">Quantity</label>
                    <input type="number" name="qyt[]" class="form-control" required>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="nilai">Nilai</label>
                    <input type="number" name="nilai[]" class="form-control" required>
                </div>
            </div>
        </div>
    `
    // $('.field-list').html(template + templateItem);
    $(this).before(templateItem)
})

$('#formNota').on('submit', function(e) {
    e.preventDefault()
    let formData = $(this).serialize()
    $.ajax({
        type: "post",
        url: URL_LIST.insertData,
        data: formData,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (response) {
            if(response.status) {
                Swal.fire({
                    title: "Berhasil!",
                    text: response.message,
                    icon: "success",
                    allowOutsideClick: false
                }).then(result => {
                    if(result.value) {
                        window.location.reload()
                    }
                })
            } else {
                Swal.fire({
                    title: "Gagal!",
                    text: response.message,
                    icon: "error",
                    allowOutsideClick: false
                }).then(result => {
                    if(result.value) {
                        window.location.reload()
                    }
                })
            }
        }
    });
})

$('#formImport').on('submit', function(e) {
    e.preventDefault()
    const data = new FormData()
    var file = $('#file_excel')[0].files[0]
    if(file == null) {
        const options = {
            icon: 'error',
            title: 'Oops!',
            text: 'Pilih file terlebih dahulu'
        }
        alertMessage('alert', options)
    } else {
        Swal.fire({
            text: "Pastikan file import sama seperti template yang sudah ditentukan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then(result => {
            data.append('file_excel', file);
            if(result.value) {
                $.ajax({
                    type: "post",
                    url: URL_LIST.importData,
                    data: data,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.import').attr('disabled', true)
                    },
                    success: function (response) {
                        if(response.status) {
                            const options = {
                                icon: 'success',
                                text: response.message,
                                title: 'Berhasil'
                            }
                            Swal.fire({
                                text : options.text,
                                title : options.title,
                                icon : options.icon,
                                allowOutsideClick: false
                            }).then(result => {
                                if(result.value) {
                                    window.location.reload()
                                }
                            })
                        } else {
                            const options = {
                                icon: 'error',
                                text: response.message,
                                title: 'Gagal'
                            }
                            Swal.fire({
                                text : options.text,
                                title : options.title,
                                icon : options.icon,
                                allowOutsideClick: false
                            }).then(result => {
                                if(result.value) {
                                    window.location.reload()
                                }
                            })
                        }
                    }
                });
            }
        })
    }
})

// ini untuk delete multiple

// $('.delMultiple').on('click', function(e) {
//     e.preventDefault()
//     var count = $('input:checkbox:checked').length
//     var checkVal = []
//     $('input:checkbox:checked').each(function() {
//         checkVal.push($(this).data('id'))
//     })
//     if(checkVal.length <= 0) {
//         const options = {
//             icon : 'error',
//             text: `Pilih data terlebih dahulu`,
//             title: 'Opps!'
//         }
//         alertMessage('alert', options);
//     } else {
//         Swal.fire({
//             title: 'Apakah anda yakin?',
//             text: `Anda ingin menghapus ${count} data secara permanen?`,
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Ya'
//         }).then(result => {
//             if(result.value) {
//                 $.ajax({
//                     type: "delete",
//                     url: URL_LIST.deleteAll,
//                     data: {
//                         listId : checkVal.join(",")
//                     },
//                     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//                     success: function (response) {
//                         if(response.status) {
//                             Swal.fire({
//                                 title: "Berhasil!",
//                                 text: response.message,
//                                 icon: "success",
//                                 allowOutsideClick: false
//                             }).then(result => {
//                                 if(result.value) {
//                                     window.location.reload()
//                                 }
//                             })
//                         } else {
//                             Swal.fire({
//                                 title: "Gagal!",
//                                 text: response.message,
//                                 icon: "error",
//                                 allowOutsideClick: false
//                             }).then(result => {
//                                 if(result.value) {
//                                     window.location.reload()
//                                 }
//                             })
//                         }
//                     }
//                 });
//             }
//         })
//     }
// })

$('.delMultiple1').on('click', function(e) {
    e.preventDefault()
    var checkVal = JSON.parse(localStorage.getItem('listMember'))
    if(checkVal.length <= 0) {
        const options = {
            icon : 'error',
            text: `Pilih data terlebih dahulu`,
            title: 'Opps!'
        }
        alertMessage('alert', options);
    } else {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: `Anda ingin menghapus ${checkVal.length} data secara permanen?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then(result => {
            if(result.value) {
                $.ajax({
                    type: "delete",
                    url: URL_LIST.deleteAll,
                    data: {
                        listId : checkVal.join(",")
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (response) {
                        if(response.status) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: response.message,
                                icon: "success",
                                allowOutsideClick: false
                            }).then(result => {
                                if(result.value) {
                                    localStorage.setItem('listMember', "[]");
                                    window.location.reload()
                                }
                            })
                        } else {
                            Swal.fire({
                                title: "Gagal!",
                                text: response.message,
                                icon: "error",
                                allowOutsideClick: false
                            }).then(result => {
                                if(result.value) {
                                    window.location.reload()
                                }
                            })
                        }
                    }
                });
            }
        })
    }
})

$('#dataTable').on('click', 'tbody tr td .delete', function(e) {
    e.preventDefault()
    let id = $(this).data('id')
    let nota = $(this).data('nota')
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: `Anda ingin menghapus nota ${nota} secara permanen?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya'
    }).then(result => {
        if(result.value) {
            $.ajax({
                type: "delete",
                url: URL_LIST.delDataByMemberId + "/" + id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    if(response.status) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: response.message,
                            icon: "success",
                            allowOutsideClick: false
                        }).then(result => {
                            if(result.value) {
                                window.location.reload()
                            }
                        })
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: response.message,
                            icon: "error",
                            allowOutsideClick: false
                        }).then(result => {
                            if(result.value) {
                                window.location.reload()
                            }
                        })
                    }
                }
            });
        }
    })
})

$('#dataTable').on('click', 'tbody tr td .update', function(e) {
    e.preventDefault()
    var memberId = $(this).data('id')
    $.ajax({
        type: "get",
        url: URL_LIST.detailData + "/" + memberId,
        success: function (response) {
            $('.field-list').html("");
            $('#member_id').val(response.member_id)
            $('#no_nota').val(response.no_nota)
            var fieldList = ``;
            $.each(response.items, function (i, value) { 
                fieldList += `
                <div class="row">
                    <div class="col-sm-6">
                        <input type="hidden" name="id[]" value="${value.id}">
                        <div class="form-group">
                            <label for="nama_barang">Nama Item</label>
                            <input type="text" name="nama_barang[]" class="form-control" value="${value.nama_barang}" required>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="qyt">Quantity</label>
                            <input type="number" name="qyt[]" class="form-control" value="${value.qyt}" required>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="nilai">Nilai</label>
                            <input type="number" name="nilai[]" class="form-control" value="${value.nilai}" required>
                        </div>
                    </div>
                </div> 
                `
            });
            fieldList += `<button class="btn btn-sm btn-success tambah-update"><i class="fas fa-fw fa-plus"></i>Tambah Item</button>`
            $('.field-list').html(fieldList)
        }
    });
})

$('#formUpdate').on('submit', function(e) {
    e.preventDefault()
    const formData = $(this).serializeArray()
    $.ajax({
        type: "put",
        url: URL_LIST.updateData,
        data: formData,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (response) {
            console.log(response);
            if(response.status) {
                Swal.fire({
                    title: "Berhasil!",
                    text: response.message,
                    icon: "success",
                    allowOutsideClick: false
                }).then(result => {
                    if(result.value) {
                        window.location.reload()
                    }
                })
            } else {
                Swal.fire({
                    title: "Gagal!",
                    text: response.message,
                    icon: "error",
                    allowOutsideClick: false
                }).then(result => {
                    if(result.value) {
                        window.location.reload()
                    }
                })
            }
        }
    });
})

$('#dataTable').on('click', 'tbody tr td .export',function(e) {
    let id = $(this).data('id')
    let nota = $(this).data('nota')
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: `Anda ingin export nota ${nota}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya'
    }).then(result => {
        if(result.value) {
            window.location.href = URL_LIST.exportNota + "/" + id
        }
    })
})

// ini untuk export multiple

// $('.expMultiple').on('click', function(e) {
//     e.preventDefault()
//     var count = $('input:checkbox:checked').length
//     var checkVal = []
//     $('input:checkbox:checked').each(function() {
//         checkVal.push($(this).data('id'))
//     })
//     if(checkVal.length <= 0) {
//         const options = {
//             icon : 'error',
//             text: `Pilih data terlebih dahulu`,
//             title: 'Opps!'
//         }
//         alertMessage('alert', options);
//     } else {
//         Swal.fire({
//             title: 'Apakah anda yakin',
//             text: `Anda ingin mengexport ${count} nota?`,
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Ya'
//         }).then(result => {
//             if(result.value) {
//                 window.location.href = URL_LIST.exportNotaMultiple + "?memberId=" + checkVal.join(",")
//             }
//         })
//     }
// })

$('.expMultiple1').on('click', function(e) {
    e.preventDefault()
    var checkVal = JSON.parse(localStorage.getItem('listMember'))
    if(checkVal.length <= 0) {
        const options = {
            icon : 'error',
            text: `Pilih data terlebih dahulu`,
            title: 'Opps!'
        }
        alertMessage('alert', options);
    } else {
        Swal.fire({
            title: 'Apakah anda yakin',
            text: `Anda ingin mengexport ${checkVal.length} nota?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then(result => {
            if(result.value) {
                localStorage.setItem('listMember', "[]");
                window.location.href = URL_LIST.exportNotaMultiple + "?memberId=" + checkVal.join(",")
            }
        })
    }
})

$('#dataTable').on('click', 'tbody tr td .export-word',function(e) {
    let id = $(this).data('id')
    let nota = $(this).data('nota')
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: `Anda ingin export nota ${nota}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya'
    }).then(result => {
        if(result.value) {
            window.location.href = URL_LIST.exportNotaToWord + "/" + id
        }
    })
})

// $('.expWordMultiple').on('click', function(e) {
//     e.preventDefault()
//     var count = $('input:checkbox:checked').length
//     var checkVal = []
//     $('input:checkbox:checked').each(function() {
//         checkVal.push($(this).data('id'))
//     })
//     if(checkVal.length <= 0) {
//         const options = {
//             icon : 'error',
//             text: `Pilih data terlebih dahulu`,
//             title: 'Opps!'
//         }
//         alertMessage('alert', options);
//     } else {
//         Swal.fire({
//             title: 'Apakah anda yakin',
//             text: `Anda ingin mengexport ${count} nota?`,
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Ya'
//         }).then(result => {
//             if(result.value) {
//                 window.location.href = URL_LIST.exportNotaToWordMultiple + "?memberId=" + checkVal.join(",")
//             }
//         })
//     }
// })
$('.expWordMultiple1').on('click', function(e) {
    e.preventDefault()
    var checkVal = JSON.parse(localStorage.getItem('listMember'))
    if(checkVal.length <= 0) {
        const options = {
            icon : 'error',
            text: `Pilih data terlebih dahulu`,
            title: 'Opps!'
        }
        alertMessage('alert', options);
    } else {
        Swal.fire({
            title: 'Apakah anda yakin',
            text: `Anda ingin mengexport ${checkVal.length} nota?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then(result => {
            if(result.value) {
                localStorage.setItem('listMember', "[]"); 
                window.location.href = URL_LIST.exportNotaToWordMultiple + "?memberId=" + checkVal.join(",")
            }
        })
    }
})

$('.showList').on('click', function(e) {
    $('.fieldData').empty()
    e.preventDefault()
    const getItemLocalStorage = JSON.parse(localStorage.getItem('listMember'));
    $.ajax({
        type: "get",
        url: URL_LIST.getNotaMultiple,
        data: {
            memberId: getItemLocalStorage.join(",")
        },
        success: function (response) {
            $.each(response, function (i, val) { 
                $('.fieldData').append(`
                <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                    Nota ${val.nota}
                    <button type="button" data-id="${val.member_id}" class="close hapus" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                `)
            });
        }
    });
})