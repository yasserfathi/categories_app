<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>
  <body>
    <div class="container">
        <main>
          <div class="p-1 m-3 text-center bg-success-subtle rounded-3">
          <div class="my-5 text-center">
                <h3 class="fw-bold text-body-emphasis">Categories</h3>
              </div>
              <div class="row p-3">
                <!--form-->
                <div class="col-md-6 col-lg-6">
                  <h4 class="mb-3">Add Categories</h4>
                    <div class="row py-3">
                        <div class="col-md-2">
                            <button type="button" class="w-100 btn btn-success btn-md" data-bs-toggle="modal" data-bs-target="#exampleModal">+</button>
                        </div>
                         <div class="col-md-10 ps-0">
                            <select class="form-select main-select" id="category" name="category" required>
                                <option value="">Choose Category</option>
                                @if(isset($categories))
                                    @foreach( $categories as $key )
                                    <option value="{{ $key->id }}">{{ $key->category_title }}</td>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row py-3" id="categoris_area"></div>
                </div>
                <!-- end form -->
                <!--table -->
                <div class="col-md-6 col-lg-6">
                    <div class="table-responsive">
                        <table class="table bg-body table-striped table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Category</th>
                              <th scope="col">SubCategory</th>
                            </tr>
                          </thead>
                          <tbody id="tbody">
                            @if(count($data) > 0)
                                @foreach( $data as $key )
                                    @if($key->subcategory_id == Null)
                                        @php
                                            $key->subcategory_id = '-';
                                        @endphp
                                    @else
                                    @php
                                        $key->subcategory_id =DB::table('category_subcategory as cat1')
                                        ->join('category_subcategory as cat2', 'cat1.id', '=', 'cat2.subcategory_id')
                                        ->select('cat1.category_title as category_title')
                                        ->where('cat2.subcategory_id',$key->subcategory_id)->first()->category_title;
                                    @endphp
                                    @endif
                                    <tr>
                                        <td>{{ $key->category_title }}</td>
                                        <td>{{ $key->subcategory_id }}</td>
                                    </tr>
                                @endforeach
                            @else
                            <tr><td colspan="2">There is No Data !!</td></tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
                </div>
                <!--end table-->
                <!--modal-->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="submit-modal" class="row g-3 needs-validation" novalidate>
                            @csrf
                            @method('')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="category_title" class="col-form-label">Category Name:</label>
                                    <input type="text" class="form-control" id="category_title" name="category_title" required>
                                    <div class="invalid-feedback">Category name is required.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit-modal" class="btn btn-xs  btn-success">Sbmit</button>
                            </div>
                        </form>
                      </div>
                    </div>
                  </div>
                <!--end modal-->
              </div>
          </div>
        </main>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>  </body>
    <script>
        $(document).ready(function(){
            $('.needs-validation').submit(function(e) {
                var form = $(this);
                var formData = new FormData($(this).get(0));
                if (!form[0].checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    form.addClass('was-validated')
                }
                else{
                    $.ajax({
                        type:'POST',
                        url:'{{ route("catgory/create") }}',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: formData,
                        processData:false,
                        contentType:false,
                        cache:false,
                        async:false,
                        success:function(data){
                            if(data == 'exists')
                            {
                                alert('Data is exists !!!')
                            }
                            else{
                                var result = data.split('|'); //explode data to select box and table rows
                                $('#category').html(result[0]);
                                $('#tbody').html(result[1]);
                                form[0].reset();
                                $('#exampleModal').modal('toggle');
                            }
                        }
                    });
                }
                return false;
            });

            $('body').delegate('select','change', function(){
                if($(this).val() != ''){
                    var _this = $(this);
                    $.post("{{ route('subcatgory/create') }}",{ "_token": "{{ csrf_token() }}",id:$(this).val(),rand:Math.random() } ,function(data){
                        if(_this.hasClass('main-select')){ //clear subcategory boxes for new selection
                            $('#categoris_area').empty();
                        }
                        var result = data.split('@');
                        if(result[0] == 'exists'){ //if subcatgory is exists
                            $('#categoris_area').append(result[1]);
                        }
                        else{  //if subcatgory is created
                            var result2 = result[1].split('|'); //explode data to select box and table rows
                            $('#categoris_area').append(result2[0]);
                            $('#tbody').html(result2[1]);
                        }

                    });
                }
            });
        });
    </script>
</html>
