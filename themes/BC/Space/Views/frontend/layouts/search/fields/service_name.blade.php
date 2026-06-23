<div class="form-group">
    <i class="field-icon fa icofont-search"></i>
    <div class="form-content">
        <label>{{ $field['title'] ?? "" }}</label>
        <div class="input-search">
            <input type="text" id="spaceservice_name" name="service_name" class="form-control" placeholder="{{__("Search for...")}}" value="{{ request()->input("service_name") }}"> 
           <div id="spacesuggestions" class="suggestions-list"></div>

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
       $('#spaceservice_name').on('keyup', function() {
        const query = $(this).val();

        if (query.length > 0) { 
            $.ajax({
                url: '{{ route("spaceservices.search") }}',
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    $('#spacesuggestions').empty();
                    if (data.length) {
                        data.forEach(service => {
                            $('#spacesuggestions').append(`<div class="suggestion-item">${service.title}</div>`);
                        });
                    }
                }
            });
        } else {
            $('#spacesuggestions').empty();  
        }
    });
    
          $(document).on('click', '.suggestion-item', function() {
        $('#spaceservice_name').val($(this).text());
        $('#spacesuggestions').empty();  
    });
});
 </script>