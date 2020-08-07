<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>Export nota penjualan</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Nota Penjualan</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1 class="text-center">Export Nota Penjualan</h1>
        <div class="row">
            <div class="col-md-10">
                <form id="formImport" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-10 col-sm-8 col-12">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_excel" name="file_excel" aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" for="inputGroupFile01">Choose file excel (.xls, xlsx)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12">
                            <button type="submit" class="btn btn-primary btn-block import">Import</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#exampleModal">
                    Input Nota
                </button>
            </div>
        </div>
        <div class="dropdown-divider"></div>
        <div class="message my-2">

        </div>
        <div class="d-flex justify-content-between mb-2">
            <button class="btn btn-sm btn-primary" onclick="window.location.href = '{{ asset('import_template.xlsx') }}'">Download template</button>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-danger btn-sm delMultiple"><span class="count"></span>Delete</button>
                <button type="button" class="btn btn-success btn-sm expMultiple"><span class="count"></span>Export Excel</button>
            </div>
        </div> 
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Member ID</th>
                        <th scope="col">No Nota</th>
                        <th scope="col">Jumlah Item</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-4"></div>
    <p class="text-center text-muted">Copyright telering.id &copy; {{ date('Y') }}</p>

    <!-- Modal Add -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Input Nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formNota">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="member_id">Member ID</label>
                                    <input type="text" name="member_id"  class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="no_nota">No Nota</label>
                                    <input type="text" name="no_nota"  class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <h6>List Items</h6>
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
                            <button class="btn btn-sm btn-success tambah"><i class="fas fa-fw fa-plus"></i>Tambah Item</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Update -->
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formUpdate">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="member_id">Member ID</label>
                                    <input type="text" name="member_id" id="member_id"  class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="no_nota">No Nota</label>
                                    <input type="text" name="no_nota" id="no_nota"  class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <h6>List Items</h6>
                            <div class="field-list">
                                
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://kit.fontawesome.com/b10279cbf9.js" crossorigin="anonymous"></script>
    <script>
        var URL_LIST = {
            getAllData : '{{ url('nota/get-all') }}',
            insertData : '{{ url('nota/add') }}',
            importData : '{{ url('nota/import') }}',
            detailData : '{{ url('nota/get/') }}',
            deleteAll : '{{ url('nota/delete-all') }}',
            delDataByMemberId : '{{ url('nota/delete/') }}',
            updateData : '{{ url('nota/update') }}',
            exportNota : '{{ url('nota/export/') }}',
            exportNotaMultiple : '{{ url('nota/export-all') }}'
        }
    </script>
    <script src="{{ asset('functions.js') }}"></script>
    <script src="{{ asset('app.js') }}"></script>
</body>
</html>