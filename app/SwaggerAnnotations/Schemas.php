<?php

namespace App\SwaggerAnnotations;

/**
 * @OA\Schema(
 * schema="UserLite",
 * title="User Lite Information",
 * description="Información básica del usuario",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Nombre Usuario")
 * )
 *
 * @OA\Schema(
 * schema="VideojuegoConRol",
 * title="Videojuego con Rol",
 * description="Información del videojuego asociado a un foro y su rol",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="nombre", type="string", example="Nombre del Videojuego"),
 * @OA\Property(property="imagen_url", type="string", format="url", example="http://example.com/image.jpg", nullable=true),
 * @OA\Property(property="rol_en_foro", type="string", enum={"principal", "secundario", "opcional"}, example="principal")
 * )
 *
 * @OA\Schema(
 * schema="RespuestaForo",
 * title="Respuesta de Mensaje de Foro",
 * description="Representación de una respuesta a un mensaje de foro",
 * @OA\Property(property="id", type="integer", example=101),
 * @OA\Property(property="contenido", type="string", example="Esta es una respuesta."),
 * @OA\Property(property="usuario", ref="#/components/schemas/UserLite"),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-04T10:30:00.000000Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-04T10:35:00.000000Z")
 * )
 *
 * @OA\Schema(
 * schema="MensajeForo",
 * title="Mensaje de Foro",
 * description="Representación de un mensaje dentro de un foro",
 * @OA\Property(property="id", type="integer", example=10),
 * @OA\Property(property="contenido", type="string", example="Contenido del mensaje principal."),
 * @OA\Property(property="usuario", ref="#/components/schemas/UserLite"),
 * @OA\Property(
 * property="respuestas",
 * type="array",
 * @OA\Items(ref="#/components/schemas/RespuestaForo")
 * ),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-04T10:00:00.000000Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-04T10:05:00.000000Z")
 * )
 *
 * @OA\Schema(
 * schema="Foro",
 * title="Foro",
 * description="Representación de un foro",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="titulo", type="string", example="Título del Foro"),
 * @OA\Property(property="descripcion", type="string", example="Descripción detallada del foro."),
 * @OA\Property(property="creado_por", ref="#/components/schemas/UserLite"),
 * @OA\Property(
 * property="videojuegos_asociados",
 * type="array",
 * @OA\Items(ref="#/components/schemas/VideojuegoConRol")
 * ),
 * @OA\Property(
 * property="mensajes",
 * type="array",
 * @OA\Items(ref="#/components/schemas/MensajeForo")
 * ),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-04T09:00:00.000000Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-04T09:05:00.000000Z")
 * )
 *
 * @OA\Schema(
 * schema="ErrorValidation",
 * title="Error de Validación",
 * description="Objeto de error devuelto en caso de fallos de validación (422)",
 * @OA\Property(property="message", type="string", example="The given data was invalid."),
 * @OA\Property(
 * property="errors",
 * type="object",
 * description="Objeto con los errores de validación por campo",
 * example={"titulo": {"El campo título es obligatorio."}}
 * )
 * )
 * @OA\Schema(
 * schema="ForoApiRequest",
 * title="Foro API Request Body",
 * description="Cuerpo de la petición para crear o actualizar un foro",
 * type="object",
 * required={"titulo", "descripcion"},
 * @OA\Property(
 * property="titulo",
 * type="string",
 * maxLength=255,
 * example="Nuevo Foro Sobre Estrategia"
 * ),
 * @OA\Property(
 * property="descripcion",
 * type="string",
 * maxLength=5000,
 * example="Discusión sobre tácticas avanzadas."
 * ),
 * @OA\Property(
 * property="videojuegosConRoles",
 * type="object",
 * description="Objeto asociativo donde la clave es el ID del videojuego y el valor es el rol. Ejemplo: {'1': 'principal', '2': 'secundario'}",
 * nullable=true,
 * example={"1": "principal", "5": "secundario"},
 * @OA\AdditionalProperties(
 * type="string",
 * enum={"principal", "secundario", "opcional"}
 * )
 * )
 * )
 *
 * @OA\Schema(
 * schema="MensajeForoStoreRequest",
 * title="Mensaje Foro Store Request Body",
 * description="Cuerpo de la petición para crear un mensaje de foro",
 * type="object",
 * required={"contenido"},
 * @OA\Property(property="contenido", type="string", example="Este es mi nuevo mensaje.")
 * )
 *
 * @OA\Schema(
 * schema="RespuestaForoStoreRequest",
 * title="Respuesta Foro Store Request Body",
 * description="Cuerpo de la petición para crear una respuesta de foro",
 * type="object",
 * required={"contenido"},
 * @OA\Property(property="contenido", type="string", example="Estoy de acuerdo con este mensaje.")
 * )
 */
class Schemas {}
