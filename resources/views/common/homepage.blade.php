@extends('layout.studentpage')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Request Management</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background: white;
            color: black;
            font-family: 'Poppins', sans-serif;
        }
        .badge {
            background: linear-gradient(135deg, #006AFF, #C13584, #FCB045);
            color: white;
            padding: 8px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
            border: 2px solid white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #006AFF, #C13584, #FCB045);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }
        .btn-primary:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
        .btn-outline-dark {
            border-radius: 30px;
            padding: 12px 25px;
            border: 2px solid black;
            transition: all 0.3s ease-in-out;
        }
        .btn-outline-dark:hover {
            background: black;
            color: white;
        }
        .hero-text h1 {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(135deg, #006AFF, #C13584, #FCB045);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .hero-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100vh;
            padding: 50px;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            position: relative;
        }
        .profile-card img {
            width: 100%;
            border-radius: 10px;
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(135deg, #006AFF, #C13584, #FCB045);
            z-index: -1;
            border-radius: 25px;
        }
        .decorative-dots {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, #006AFF 20%, transparent 20%);
            background-size: 10px 10px;
        }
        .partition {
            width: 100%;
            height: 20px;
            background: linear-gradient(135deg, #006AFF, #C13584, #FCB045);
            margin-top: 50px;
        }
        .carousel-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container hero-container">
        <div class="hero-text">
            <span class="badge">Request · Track · Manage</span>
            <h1 class="mt-3">Request Your Documents Online</h1>
            <p class="text-dark">Easily request and track your documents<br>with our Online Document Request and Management System for UBNHS.</p>
            <div>
                <a href="{{ route('studentrequest.create') }}" class="btn btn-primary">Request Now</a>
                <a href="{{ route('st.page') }}" class="btn btn-outline-dark">Track Request</a>
            </div>
        </div>
        <div class="profile-card">
            <img src="/images/ubnhs.png" alt="Document" class="img-fluid">
            <div class="decorative-dots"></div>
        </div>
    </div>

    <!-- Partition -->
    <div class="partition"></div>

  <!-- About the System Section -->
<div class="container mt-5">
    <div class="p-4 rounded shadow-sm border" style="border-image-source: linear-gradient(135deg, #006AFF, #C13584, #FCB045); border-image-slice: 1;">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="text-gradient">About the System</h2>
                <p class="text-dark">
                    The Online Document Request and Management System for Upper Bicutan National High School (UBNHS)
                    is designed to streamline the process of requesting, tracking, and managing student documents.
                    This system allows students to conveniently request their documents online and monitor the status
                    of their requests in real-time. It aims to reduce manual processes and enhance service efficiency
                    for the registrar's office.
                </p>
            </div>
            <div class="col-md-6">
                <div id="carouselExampleAutoplaying" class="carousel slide mt-3" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/images/ssg1.jpg" class="d-block w-100 rounded" alt="Slide 1">
                        </div>
                        <div class="carousel-item">
                            <img src="/images/ssg2.jpg" class="d-block w-100 rounded" alt="Slide 2">
                        </div>
                        <div class="carousel-item">
                            <img src="/images/ssg3.jpg" class="d-block w-100 rounded" alt="Slide 3">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Promotion Section -->
<div class="container mt-5">
    <div class="p-4 rounded shadow-sm border" style="border-image-source: linear-gradient(135deg, #006AFF, #C13584, #FCB045); border-image-slice: 1;">
        <h2 class="text-center mb-4 text-gradient">Video Promotion</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <iframe class="card-img-top" src="https://www.youtube.com/embed/VIDEO_ID1" allowfullscreen></iframe>
                    <div class="card-body text-center">
                        <h5 class="card-title">How to Request a Document</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <iframe class="card-img-top" src="https://www.youtube.com/embed/VIDEO_ID2" allowfullscreen></iframe>
                    <div class="card-body text-center">
                        <h5 class="card-title">Tracking Your Request</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <iframe class="card-img-top" src="https://www.youtube.com/embed/VIDEO_ID3" allowfullscreen></iframe>
                    <div class="card-body text-center">
                        <h5 class="card-title">Promotional Video</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
@endsection
