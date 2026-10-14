import 'package:dio/dio.dart';

class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? errors;

  const ApiException({required this.message, this.statusCode, this.errors});

  factory ApiException.fromDio(DioException exception) {
    final response = exception.response;

    if (response != null) {
      final data = response.data;

      if (data is Map) {
        final mappedData = Map<String, dynamic>.from(data);

        final rawErrors = mappedData['errors'];

        Map<String, dynamic>? errors;

        if (rawErrors is Map) {
          errors = Map<String, dynamic>.from(rawErrors);
        }

        String message = mappedData['message']?.toString() ?? 'Request failed.';

        if (errors != null && errors.isNotEmpty) {
          final firstError = errors.values.first;

          if (firstError is List && firstError.isNotEmpty) {
            message = firstError.first.toString();
          } else {
            message = firstError.toString();
          }
        }

        return ApiException(
          message: message,
          statusCode: response.statusCode,
          errors: errors,
        );
      }

      return ApiException(
        message: 'Request failed.',
        statusCode: response.statusCode,
      );
    }

    switch (exception.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return const ApiException(
          message: 'Connection timed out. Please try again.',
        );

      case DioExceptionType.connectionError:
        return const ApiException(
          message: 'Could not connect to the AST-CV server.',
        );

      default:
        return const ApiException(
          message: 'Unexpected network error occurred.',
        );
    }
  }

  @override
  String toString() {
    return message;
  }
}
