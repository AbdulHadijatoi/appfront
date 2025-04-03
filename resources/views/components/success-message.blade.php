@if(session('success'))
    <div class="success-message" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
        {{ session('success') }}
    </div>
@endif
