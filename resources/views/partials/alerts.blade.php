@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @php(session()->forget('success'))
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @php(session()->forget('error'))
@endif

@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const successAlert = document.getElementById('alert-success');
    const errorAlert = document.getElementById('alert-error');

    [successAlert, errorAlert].forEach(alert => {
      if (alert) {
        setTimeout(() => {
          alert.classList.remove('show');
          alert.classList.add('fade');
          setTimeout(() => alert.remove(), 500);
        }, 4000); 
      }
    });
  });
</script>
