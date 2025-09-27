@if(session('Success') || session('Warning') || session('Status') || session('Error') || session('Danger') || session('Info') || session('Declined') || $errors->has('password'))
<script>
    Swal.fire({
        icon: '{{ 
            session('Warning') ? 'warning' : 
            (session('Info') ? 'info' : 
            ((session('Error') || session('Danger') || $errors->has('password')) ? 'error' : 'success')) 
        }}',
        title: 'Notice!',
        text: "{{ ucfirst(
            session('Success') 
            ?? session('Status') 
            ?? session('Error') 
            ?? session('Danger') 
            ?? session('Warning') 
            ?? session('Info') 
            ?? session('Declined') 
            ?? $errors->first('password')
        ) }}",
        confirmButtonColor: '#1dd3b0',
        background: '#1F2937', // custom dark gray background
        color: '#fff', // text color for contrast
    });
</script>
@endif
