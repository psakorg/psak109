<head>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Poppeer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>
body {
        margin: 0;
        padding: 0;
    }
    .container{
        display: flex;
        height: 60vh; /* Adjust height to account for header */
    }
    .content{
        flex: 1; /* Take the remaining space */
        overflow: hidden; /* Prevent overflow */
        display: flex; /* Use flexbox for content */
        justify-content: center; /* Center the image horizontally */
        align-items: center; /* Center the image vertically */
        margin-left: 150px;
        margin-top: 50px;
    }
    .card{
        width: 300px;
        height: 200px;
        padding: 10px;
        display: flex;
        padding-left: 20px;
        justify-content: center;
        margin-bottom: 90px;
    }
    .button{
        display: flex;
        text-align: right;
        justify-content: right;
        align-items: right;
        padding-right: 20px
    }
    h2{
        margin-top: 80px; /* Adjust this value as needed */
        text-align: center; /* Center the text if desired */
        padding: 10px; /* Optional: Add some padding */
        margin-left: 150px;
        font-weight: bold;
        color: blue; 
    }
</style>
<body>
<h2>UPLOAD DASHBOARD IMAGE</h2>
<div class="container">
<div class="content">
@if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
@endif
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li style="color: red;">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
<div class="card">
    <form action="{{ route('dashboard.image') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
        <label for="image" class="form-label">Choose an Image:</label>
        <input class="form-control" type="file" id="image" name="image">
        </div>
        <div class="button">
        <button class="btn btn-primary" type="submit">Upload Image</button>
        </div>
    </form>
</div>
</div>
</div>
</body>
</html>