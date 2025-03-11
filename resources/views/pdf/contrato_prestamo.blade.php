<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contrato de Préstamo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .header {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 20px;
        }

        .sub-header {
            text-align: center;
            font-size: 18px;
            font-weight: normal;
            color: #555;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 25px;
            font-size: 16px;
            color: #333;
        }

        .section strong {
            color: #003366;
        }

        .terms {
            margin-bottom: 30px;
        }

        .terms ul {
            list-style-type: none;
            padding-left: 20px;
        }

        .terms li {
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #555;
        }

        .firma {
            text-align: center;
            margin-top: 50px;
        }

        .firma p {
            margin: 0;
            padding: 0;
            font-size: 16px;
        }

        .signature-line {
            border-top: 2px solid #000;
            width: 200px;
            margin: 0 auto;
            padding: 5px 0;
        }

        .contact-info {
            margin-top: 30px;
            font-size: 14px;
            text-align: center;
        }

        .contact-info p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">Contrato de Préstamo</div>
        <div class="sub-header">Este acuerdo está sujeto a los términos y condiciones establecidos por nuestra entidad.</div>

        <div class="section">
            <p><strong>Cliente:</strong> {{ $prestamo->cliente->nombre }}</p>
            <p><strong>Monto Aprobado:</strong> ${{ number_format($prestamo->monto_aprobado, 2) }}</p>
            <p><strong>Fecha de Aprobación:</strong> {{ $prestamo->fecha_de_aprobacion }}</p>
            <p><strong>Fecha de Vencimiento:</strong> {{ \Carbon\Carbon::parse($prestamo->fecha_de_aprobacion)->addMonths(12)->format('d-m-Y') }}</p>
        </div>

        <div class="terms">
            <p><strong>Términos y Condiciones:</strong></p>
            <ul>
                <li>El cliente se compromete a pagar el monto aprobado en el tiempo estipulado.</li>
                <li>El monto de cada cuota será calculado con la tasa de interés establecida.</li>
                <li>En caso de atraso, el cliente será responsable de las penalizaciones acordadas previamente.</li>
                <li>Este contrato es válido solo si está firmado por ambas partes.</li>
            </ul>
        </div>

        <div class="firma">
            <p class="signature-line"></p>
            <p><strong>Firma del Cliente</strong></p>
        </div>

        <div class="firma">
            <p class="signature-line"></p>
            <p><strong>Firma del Representante de la Institución</strong></p>
        </div>

        <div class="footer">
            <p><strong>Corporación Financiera XYZ S.A.</strong></p>
            <p>Dirección: Av. Principal #123, Ciudad, País</p>
            <p>Teléfono: +1 (800) 123-4567</p>
            <p>Email: contacto@xyzfinanciera.com</p>
        </div>

        <div class="contact-info">
            <p>Para cualquier consulta, por favor comuníquese con nosotros.</p>
            <p><strong>Nota:</strong> Este documento es confidencial y solo debe ser compartido con personas autorizadas.</p>
        </div>
    </div>
</body>

</html>
