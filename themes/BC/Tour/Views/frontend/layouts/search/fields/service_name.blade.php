<div class="form-group">
    <i class="field-icon fa icofont-search"></i>
    <div class="form-content">
        <label>{{ $field['title'] ?? "" }}</label>
        <div class="input-search">
            <input type="text" id="tourservice_name" name="service_name" class="form-control" placeholder="{{__("Search for...")}}" value="{{ request()->input("service_name") }}"> 
           <div id="toursuggestions" class="suggestions-list"></div>

        </div>
    </div>
</div>

<style>
    .suggestions-list {
    border: 1px solid #ccc;
    max-height: 200px;
    overflow-y: auto;
    position: absolute;
    z-index: 1000;
    background: white;
}

.suggestion-item {
    padding: 10px;
    cursor: pointer;
}

.suggestion-item:hover {
    background-color: #f0f0f0;
} 
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
 <script>
     $(document).ready(function() {
       $('#tourservice_name').on('keyup', function() {
        const query = $(this).val();

        if (query.length > 0) { 
            $.ajax({
                url: '{{ route("tourservices.search") }}',
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    $('#toursuggestions').empty();
                    if (data.length) {
                        data.forEach(service => {
                            $('#toursuggestions').append(`<div class="suggestion-item">${service.title}</div>`);
                        });
                    }
                }
            });
        } else {
            $('#toursuggestions').empty();  
        }
    });
    
          $(document).on('click', '.suggestion-item', function() {
        $('#tourservice_name').val($(this).text());
        $('#toursuggestions').empty();  
    });
});
 </script>