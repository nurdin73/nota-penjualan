<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Export nota penjualan</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .floating {
            position: fixed;
            bottom: 10px;
            left: 30px;
        }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <a class="navbar-brand" href="#">Nota Penjualan</a>
      </div>
    </nav>

    <div class="d-flex justify-content-center align-items-center vh-100">
      <div class="card w-50">
        <div class="card-header">
          <span class="card-title">Upload file excel disini</span>
        </div>
        <div class="card-body">
          @if (session('message')) 
            <div class="alert alert-success">{{ session('message') }}</div>
          @endif
          <form action="{{ route('printNotaNew') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="date">Pilih Bulan nota</label>
                  <input type="text" name="date" id="date" class="form-control @error('date') is-invalid @enderror">
                  @error('date')
                  <small class="form-text text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="excel">Pilih file excel <sub>(.csv)</sub></label>
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" name="excel" id="excel" class="custom-file-input @error('excel') is-invalid @enderror" id="inputGroupFile02" >
                      <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02">Pilih file csv</label>
                    </div>
                  </div>
                  @error('excel')
                  <small class="form-text text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>
            <button class="btn btn-danger" type="submit">Cetak</button>
            <button class="btn btn-light" type="button" id="preview">Preview</button>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="previewModalLabel">Preview</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-sm table-borderless table-striped table-hover">
                <thead>
                  <tr>
                    <th style="width: 10%">No</th>
                    <th>Member ID</th>
                    <th style="width: 30%; text-align:right">Nominal Baru</th>
                  </tr>
                </thead>
                <tbody id="dataset">
                  {{-- <tr>
                    <td>1</td>
                    <td>TRHPA4218 (TRHP) - 6287874968695</td>
                    <td align="right">5000000</td>
                  </tr> --}}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js" integrity="sha512-zHDWtKP91CHnvBDpPpfLo9UsuMa02/WgXDYcnFp5DFs8lQvhCe2tx56h2l7SqKs/+yQCx4W++hZ/ABg8t3KH/Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
      var csvParsedArray = [];
      $(document).ready(function () {
        $('#date').datepicker({
          format: 'yyyy/mm',
          startView: "months", 
          minViewMode: "months"
        })
        $('#excel').on('change', function(e) {
          const file = e.target.files
          if(file[0].name.toLowerCase().lastIndexOf('.csv') == -1) {
              alert('file harus berekstensi .csv')
              return
          }
          $('.custom-file-label').text(file[0].name)
        })
        $('#preview').on('click', function(e) {
          e.preventDefault()
          // $('#previewModal').modal('show')
          const file = $('#excel')[0]
          if(file.files.length == 0) {
            alert('silahkan upload file terlebih dahulu')
            return
          } 
          const files = file.files
          if(files[0].name.toLowerCase().lastIndexOf('.csv') == -1) {
              alert('file harus berekstensi .csv')
              return
          }

          const reader = new FileReader()
          const bytes = 50000;
          reader.onloadend = (e) => {
            let lines = e.target.result
            if(lines && lines.length > 0) {
              let lineArray = CSVToArray(lines)
              if(lines.length == bytes) {
                lineArray = lineArray.splice(0, lineArray.length - 1)
              }
              var columnArray = [],
                  stringHeader = `<thead><tr>`,
                  stringBody = ``,
                  no = 0
              for (let i = 0; i < lineArray.length; i++) {
                let cellArray = lineArray[i];
                stringBody += `<tr>`
                if(i !== 0) {
                  if(cellArray.length > 1) {
                    no++;
                    cellArray = [no].concat(cellArray);
                    cellArray.splice(2, 1)
                    for (let j = 0; j < cellArray.length; j++) {
                      let cell = cellArray[j];
                      if(j == 2) {
                        stringBody += `<td align="right">Rp. ${parseInt(cell).toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.")}</td>`
                      } else if(j == 1) {
                        const getMemberId = cell.split(' ')
                        stringBody += `<td>${getMemberId[0]}</td>`
                      } else {
                        stringBody += `<td>${cell}</td>`
                      }
                      csvParsedArray.push({
                        column: columnArray[i],
                        value: cellArray[j]
                      })
                    }
                  }
                }
                stringBody += `</tr>`
              }
              $('#dataset').html(stringBody)
            }
          }
          let blob = files[0].slice(0, bytes);
          reader.readAsBinaryString(blob);
          $('#previewModal').modal('show')
        })
      });

      function CSVToArray(strData, strDelimiter) {
        strDelimiter = (strDelimiter || ",");
        let objPattern = new RegExp(
          (
            "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +
            "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
            "([^\"\\" + strDelimiter + "\\r\\n]*))"
            ),
          "gi"
          );
        let arrData = [[]];
        let arrMatches = null;
        while (arrMatches = objPattern.exec(strData)) {
          let strMatchedDelimiter = arrMatches[1];
          let strMatchedValue = [];
          if (strMatchedDelimiter.length && (strMatchedDelimiter != strDelimiter)) {
            arrData.push([]);
          }
          if (arrMatches[2]) {
            strMatchedValue = arrMatches[2].replace(new RegExp("\"\"", "g"),"\"");
          } else {
            strMatchedValue = arrMatches[3];
          }
          arrData[arrData.length - 1].push(strMatchedValue);
        }
        return (arrData);
      }
    </script>
  </body>
</html>