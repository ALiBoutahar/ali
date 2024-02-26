@extends('app')
@section('main')

    <!-- Modal create -->
    <div class="modal fade" id="create" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="nom" name="nom" placeholder="nom" required>
                    <input type="text" class="form-control mb-3" id="prenom" name="prenom" placeholder="prenom" required>
                    <input type="text" class="form-control mb-3" id="phone" name="phone" placeholder="phone" required>
                    <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="createClient()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal detail -->
    <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Details Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <input type="text" class="form-control mb-3" id="edit_nom" name="nom" placeholder="nom" disabled>
                    <input type="text" class="form-control mb-3" id="edit_prenom" name="prenom" placeholder="prenom" disabled>
                    <input type="text" class="form-control mb-3" id="edit_phone" name="phone" placeholder="phone" disabled>
                    <input type="email" class="form-control" id="edit_email" name="email" placeholder="email" disabled>
                </div>
                <div class="modal-footer">
                    <button type="button" id="save" hidden class="btn btn-primary" onclick="updateClient()">Save</button>
                    <button type="button" id="x" class="btn btn-info" onclick="afficher()">Edit</button>
                    <button type="button" id="y" hidden class="btn btn-danger" onclick="nafficher()">Close</button>  
                </div>
            </div>
        </div>
        <script>  
            function afficher(){
                $('#x').prop('hidden', true);
                $('#y').prop('hidden', false);
                $('#edit_nom').prop('disabled', false);
                $('#edit_prenom').prop('disabled', false);
                $('#edit_phone').prop('disabled', false);
                $('#edit_email').prop('disabled', false);
                $('#save').prop('hidden', false);
            };
            function nafficher(){
                $('#x').prop('hidden', false);
                $('#y').prop('hidden', true);
                $('#edit_nom').prop('disabled', true);
                $('#edit_prenom').prop('disabled', true);
                $('#edit_phone').prop('disabled', true);
                $('#edit_email').prop('disabled', true);
                $('#save').prop('hidden', true);
            };
        </script>
    </div>

    <!-- DataTable -->
    <div class="container border shadow rounded mt-3">
        <div class="row">
            <div class="col-md-12 p-3">
                <div class="col-md-12 d-flex justify-content-end pb-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create">Add Clients</button>
                    <a href="{{url('clients/pdf')}}" class="btn btn-info btn-sm ms-2" onclick="return confirm('Are you sure you want to download the PDF?')">Télécharger PDF</a>
                </div>
                <table id="myTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr class="text-center bg-light">
                            <th>Id</th>
                            <th>Nom</th>
                            <th>Prenom</th>
                            <th>Phone</th>   
                            <th>E-mail</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Function With Ajax -->
    <script>
        
        var table = $('#myTable').DataTable();
        getclients()

        //***********************************************************************************
        function getclients() {
            $.ajax({
                type: 'get',
                url: '{{ url("/get_clients") }}',
                success: function(data) {
                    console.log(data);
                    table.clear().draw();
                    if (data.success && data.clients) {
                        $.each(data.clients, function(index, a) {
                            table.row.add([
                                a.id,
                                a.nom,
                                a.prenom,
                                a.phone,
                                a.email,
                                '<button class="btn btn-warning btn-sm me-1" onclick="detailClient(' + a.id + ')">Detail</button>' +
                                '<button class="btn btn-danger btn-sm me-1" onclick="deleteClient(' + a.id + ')">Delete</button>'
                            ]).draw();
                        });
                    } else {
                        console.error(" error! ");
                        toastr.error('Error');
                    }
                }
            });
        };

        //***********************************************************************************
        function createClient() {
            var nom = $('#nom').val();
            var prenom = $('#prenom').val();
            var phone = $('#phone').val();
            var email = $('#email').val();
            $.ajax({
                type: 'POST',
                url: '{{ url("/store") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    nom: nom,
                    prenom : prenom,
                    phone : phone,
                    email : email,
                },
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        toastr.success('Client added successfully');
                        getclients()
                    } else {
                        toastr.error('Error');
                    }
                    $('#nom').val('');
                    $('#prenom').val('');
                    $('#phone').val('');
                    $('#email').val('');
                }, 
            });
        }

        //***********************************************************************************
        function detailClient(id) {
            $.ajax({
                type: 'GET',
                url: '{{ url("/detail") }}' + '/' + id,
                success: function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_nom').val(data.nom);
                    $('#edit_prenom').val(data.prenom);
                    $('#edit_phone').val(data.phone);
                    $('#edit_email').val(data.email);
                    $('#edit').modal('show');
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred while fetching client data');
                }
            });
        }

        //***********************************************************************************
        function updateClient() {
            var id = $('#edit_id').val();
            var nom = $('#edit_nom').val();
            var prenom = $('#edit_prenom').val();
            var phone = $('#edit_phone').val();
            var email = $('#edit_email').val();
            $.ajax({
                type: 'POST',
                url: '/update',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    nom: nom,
                    prenom: prenom,
                    phone: phone,
                    email: email,
                },
                success: function(data) {
                    table.clear().draw();
                    if (data.success) {
                        getclients()
                        $('#x').prop('hidden', false);
                        $('#y').prop('hidden', true);
                        $('#edit_nom').prop('disabled', true);
                        $('#edit_prenom').prop('disabled', true);
                        $('#edit_phone').prop('disabled', true);
                        $('#edit_email').prop('disabled', true);
                        $('#save').prop('hidden', true);
                        toastr.success('Client updated successfully');
                    }
                    else {
                        toastr.error('Failed to update client');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred while updating the client');
                }
            });
        }

        //***********************************************************************************
        function deleteClient(id) {
            if (confirm("Are you sure you want to delete this client?")) {
                $.ajax({
                    type: 'POST',
                    url: '/delete',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            var rowIndex = table.row($('#' + id)).index();
                            table.row(rowIndex).remove().draw();
                            toastr.success('Client deleted successfully');
                        } else {
                            toastr.error('Failed to delete client');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred while deleting the client');
                    }
                });
            }
        }

        //***********************************************************************************
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        });
    </script>

@endsection