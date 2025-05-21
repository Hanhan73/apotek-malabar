<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Apotek Malabar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            padding: 20px;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .report-header h1 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-header h2 {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 15px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th, 
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        .table-summary {
            margin-top: 20px;
            margin-bottom: 40px;
        }
        
        .report-footer {
            margin-top: 50px;
            text-align: right;
        }
        
        @page {
            size: A4;
            margin: 2cm;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="report-header">
        <h1>APOTEK MALABAR</h1>
        <h2>@yield('title')</h2>
        <p>@yield('subtitle')</p>
    </div>
    
    <div class="container-fluid">
        @yield('content')
    </div>
    
    <div class="report-footer">
        <p>Bandung, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <br><br><br>
        <p>(______________________)</p>
        <p>Manager Apotek</p>
    </div>
    
    @stack('scripts')
</body>
</html>