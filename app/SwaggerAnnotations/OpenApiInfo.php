<?php

namespace App\SwaggerAnnotations;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="API de Foros, Mensajes y Respuestas",
 * description="Documentación de la API para la gestión de foros, mensajes, respuestas y autenticación.",
 * @OA\Contact(
 * email="1813370@alu.murciaeduca.es"
 * ),
 * @OA\License(
 * name="Apache 2.0",
 * url="http://www.apache.org/licenses/LICENSE-2.0.html"
 * )
 * )
 *
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Servidor Principal de la API"
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * description="Autenticación por Token Bearer (Sanctum)"
 * )
 *
 * @OA\Tag(
 * name="Autenticación",
 * description="Endpoints para login y logout de usuarios"
 * )
 * @OA\Tag(
 * name="Foros",
 * description="Operaciones CRUD para Foros"
 * )
 * @OA\Tag(
 * name="Mensajes de Foro",
 * description="Operaciones CRUD para Mensajes dentro de un Foro"
 * )
 * @OA\Tag(
 * name="Respuestas de Mensaje",
 * description="Operaciones CRUD para Respuestas a Mensajes"
 * )
 */
class OpenApiInfo
{
}
