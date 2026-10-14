import 'package:dio/dio.dart';

import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';
import '../../../core/api/api_exception.dart';
import '../../resume_builder/models/resume_detail.dart';
import '../../resume_builder/models/resume_education.dart';
import '../../resume_builder/models/resume_experience.dart';
import '../../resume_builder/models/resume_version.dart';
import '../models/resume_item.dart';

class ResumeRepository {
  final ApiClient apiClient;

  ResumeRepository({required this.apiClient});

  Map<String, dynamic> _asMap(dynamic value) {
    if (value is Map<String, dynamic>) {
      return value;
    }

    if (value is Map) {
      return Map<String, dynamic>.from(value);
    }

    return <String, dynamic>{};
  }

  String? _nullableText(String? value) {
    final trimmed = value?.trim() ?? '';

    if (trimmed.isEmpty) {
      return null;
    }

    return trimmed;
  }

  String? _formatApiDate(DateTime? date) {
    if (date == null) {
      return null;
    }

    final year = date.year.toString().padLeft(4, '0');

    final month = date.month.toString().padLeft(2, '0');

    final day = date.day.toString().padLeft(2, '0');

    return '$year-$month-$day';
  }

  // ------------------------------------------------------------
  // RESUMES
  // ------------------------------------------------------------

  Future<List<ResumeItem>> getResumes() async {
    try {
      final response = await apiClient.dio.get(ApiEndpoints.resumes);

      final responseData = _asMap(response.data);

      final data = responseData['data'];

      if (data is! List) {
        return [];
      }

      return data
          .whereType<Map>()
          .map((item) => ResumeItem.fromJson(Map<String, dynamic>.from(item)))
          .toList();
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeDetail> getResume(int resumeId) async {
    try {
      final response = await apiClient.dio.get(ApiEndpoints.resume(resumeId));

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeDetail.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeItem> createResume({required String title}) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumes,
        data: {'title': title.trim()},
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeItem.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeItem> updateResume({
    required int resumeId,
    required String title,
  }) async {
    try {
      final response = await apiClient.dio.put(
        ApiEndpoints.resume(resumeId),
        data: {'title': title.trim()},
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeItem.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<void> deleteResume(int resumeId) async {
    try {
      await apiClient.dio.delete(ApiEndpoints.resume(resumeId));
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ------------------------------------------------------------
  // RESUME VERSIONS
  // ------------------------------------------------------------

  Future<ResumeVersionModel> createVersion({required int resumeId}) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumeVersions(resumeId),
        data: const <String, dynamic>{},
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeVersionModel.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeVersionModel> getVersion({
    required int resumeId,
    required int versionId,
  }) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.resumeVersion(resumeId, versionId),
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeVersionModel.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeVersionModel> updateVersion({
    required int resumeId,
    required int versionId,
    String? summary,
    String? template,
  }) async {
    try {
      final payload = <String, dynamic>{'summary': summary};

      if (template != null) {
        payload['template'] = template;
      }

      final response = await apiClient.dio.put(
        ApiEndpoints.resumeVersion(resumeId, versionId),
        data: payload,
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeVersionModel.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ------------------------------------------------------------
  // EXPERIENCES
  // ------------------------------------------------------------

  Future<List<ResumeExperience>> getExperiences({
    required int resumeId,
    required int versionId,
  }) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.resumeExperiences(resumeId),
      );

      final responseData = _asMap(response.data);

      final data = responseData['data'];

      if (data is! List) {
        return [];
      }

      final experiences = data
          .whereType<Map>()
          .map(
            (item) =>
                ResumeExperience.fromJson(Map<String, dynamic>.from(item)),
          )
          .where((experience) => experience.resumeVersionId == versionId)
          .toList();

      experiences.sort((first, second) {
        final orderComparison = first.sortOrder.compareTo(second.sortOrder);

        if (orderComparison != 0) {
          return orderComparison;
        }

        final firstDate = first.startDate ?? DateTime(1900);

        final secondDate = second.startDate ?? DateTime(1900);

        return secondDate.compareTo(firstDate);
      });

      return experiences;
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeExperience> getExperience({
    required int resumeId,
    required int experienceId,
  }) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.resumeExperience(resumeId, experienceId),
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeExperience.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeExperience> createExperience({
    required int resumeId,
    required int versionId,
    required String companyName,
    required String jobTitle,
    String? location,
    DateTime? startDate,
    DateTime? endDate,
    required bool isCurrent,
    String? description,
    int sortOrder = 0,
  }) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumeExperiences(resumeId),
        data: {
          'resume_version_id': versionId,
          'company_name': companyName.trim(),
          'job_title': jobTitle.trim(),
          'location': _nullableText(location),
          'start_date': _formatApiDate(startDate),
          'end_date': isCurrent ? null : _formatApiDate(endDate),
          'is_current': isCurrent,
          'description': _nullableText(description),
          'sort_order': sortOrder,
        },
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeExperience.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeExperience> updateExperience({
    required int resumeId,
    required int experienceId,
    required String companyName,
    required String jobTitle,
    String? location,
    DateTime? startDate,
    DateTime? endDate,
    required bool isCurrent,
    String? description,
    int sortOrder = 0,
  }) async {
    try {
      final response = await apiClient.dio.put(
        ApiEndpoints.resumeExperience(resumeId, experienceId),
        data: {
          'company_name': companyName.trim(),
          'job_title': jobTitle.trim(),
          'location': _nullableText(location),
          'start_date': _formatApiDate(startDate),
          'end_date': isCurrent ? null : _formatApiDate(endDate),
          'is_current': isCurrent,
          'description': _nullableText(description),
          'sort_order': sortOrder,
        },
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeExperience.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<void> deleteExperience({
    required int resumeId,
    required int experienceId,
  }) async {
    try {
      await apiClient.dio.delete(
        ApiEndpoints.resumeExperience(resumeId, experienceId),
      );
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ------------------------------------------------------------
  // EDUCATIONS
  // ------------------------------------------------------------

  Future<List<ResumeEducation>> getEducations({
    required int resumeId,
    required int versionId,
  }) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.resumeEducations(resumeId),
      );

      final responseData = _asMap(response.data);

      final data = responseData['data'];

      if (data is! List) {
        return [];
      }

      final educations = data
          .whereType<Map>()
          .map(
            (item) => ResumeEducation.fromJson(Map<String, dynamic>.from(item)),
          )
          .where((education) => education.resumeVersionId == versionId)
          .toList();

      educations.sort((first, second) {
        final orderComparison = first.sortOrder.compareTo(second.sortOrder);

        if (orderComparison != 0) {
          return orderComparison;
        }

        final firstDate = first.startDate ?? DateTime(1900);

        final secondDate = second.startDate ?? DateTime(1900);

        return secondDate.compareTo(firstDate);
      });

      return educations;
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeEducation> getEducation({
    required int resumeId,
    required int educationId,
  }) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.resumeEducation(resumeId, educationId),
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeEducation.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeEducation> createEducation({
    required int resumeId,
    required int versionId,
    required String institution,
    String? degree,
    String? fieldOfStudy,
    String? location,
    DateTime? startDate,
    DateTime? endDate,
    String? description,
    int sortOrder = 0,
  }) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumeEducations(resumeId),
        data: {
          'resume_version_id': versionId,
          'institution': institution.trim(),
          'degree': _nullableText(degree),
          'field_of_study': _nullableText(fieldOfStudy),
          'location': _nullableText(location),
          'start_date': _formatApiDate(startDate),
          'end_date': _formatApiDate(endDate),
          'description': _nullableText(description),
          'sort_order': sortOrder,
        },
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeEducation.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<ResumeEducation> updateEducation({
    required int resumeId,
    required int educationId,
    required String institution,
    String? degree,
    String? fieldOfStudy,
    String? location,
    DateTime? startDate,
    DateTime? endDate,
    String? description,
    int sortOrder = 0,
  }) async {
    try {
      final response = await apiClient.dio.put(
        ApiEndpoints.resumeEducation(resumeId, educationId),
        data: {
          'institution': institution.trim(),
          'degree': _nullableText(degree),
          'field_of_study': _nullableText(fieldOfStudy),
          'location': _nullableText(location),
          'start_date': _formatApiDate(startDate),
          'end_date': _formatApiDate(endDate),
          'description': _nullableText(description),
          'sort_order': sortOrder,
        },
      );

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      return ResumeEducation.fromJson(data);
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<void> deleteEducation({
    required int resumeId,
    required int educationId,
  }) async {
    try {
      await apiClient.dio.delete(
        ApiEndpoints.resumeEducation(resumeId, educationId),
      );
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }
}
