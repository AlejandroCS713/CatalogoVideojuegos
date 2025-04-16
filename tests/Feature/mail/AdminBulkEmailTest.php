<?php
use App\Mail\AdminBulkEmail;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

it('builds the admin bulk email correctly', function () {
    $mensaje = 'Este es un mensaje de prueba';

    $email = new AdminBulkEmail($mensaje);

    expect($email->envelope()->subject)->toBe('Un mensaje importante de la administración');

    expect($email->content()->markdown)->toBe('emails.admin.bulk');

    expect($email->content()->with['messageBody'])->toBe($mensaje);
});
