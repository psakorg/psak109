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
        height: 90vh; /* Adjust height to account for header */
        margin-left: 80px;

    }
    .content{
        flex: 1; /* Take the remaining space */
        overflow: hidden; /* Prevent overflow */
        display: flex; /* Use flexbox for content */
        justify-content: center; /* Center the image horizontally */
        align-items: center; /* Center the image vertically */
        margin-left: 100px;
        margin-top: 50px;
    }
    .image-container{
        width: 100%; /* Ensure the container does not exceed the width */
        height: 100%; /* Adjust height to account for header and navbar */
        overflow: hidden; /* Prevent overflow */
        display: flex; /* Right the image */
        justify-content: right; /* Right horizontally */
        align-items: right; /* Right vertically */
        margin: 0;
        padding: 0;
    }
    .image-container img{
        width: 182vh;
        height: 100%;
        margin: 0;
        padding: 0;
    }
</style>
@if(isset($base64Image))
<div class="container">
<div class="content">
<div class="image-container">
        <img src="data:{{ $mimeType }};base64,{{ $base64Image }}" alt="{{ $imgname }}" />
@else
        <h2>No image found.</h2>
@endif
</div>
</div>
</div>
