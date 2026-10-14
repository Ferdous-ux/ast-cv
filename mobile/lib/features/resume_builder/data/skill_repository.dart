import 'package:dio/dio.dart';

import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';
import '../../../core/api/api_exception.dart';
import '../models/resume_skill_item.dart';
import '../models/skill_option.dart';

class SkillRepository {
  final ApiClient apiClient;

  SkillRepository({required this.apiClient});

  Map<String, dynamic> _asMap(dynamic value) {
    if (value is Map<String, dynamic>) {
      return value;
    }

    if (value is Map) {
      return Map<String, dynamic>.from(value);
    }

    return <String, dynamic>{};
  }

  // ============================================================
  // 1. Get all reference skills
  // ============================================================

  Future<List<SkillOption>> getReferenceSkills() async {
    try {
      final response = await apiClient.dio.get(ApiEndpoints.referenceSkills);

      final root = _asMap(response.data);
      final data = root['data'];

      if (data is! List) {
        return [];
      }

      return data
          .whereType<Map>()
          .map((item) => SkillOption.fromJson(Map<String, dynamic>.from(item)))
          .toList();
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ============================================================
  // 2. Get skills attached to a specific resume version
  // ============================================================

  Future<List<ResumeSkillItem>> getResumeSkills({
    required int resumeId,
    required int versionId,
  }) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.resumeSkills(resumeId),
      );

      final root = _asMap(response.data);
      final data = root['data'];

      if (data is! List) {
        return [];
      }

      final skills = data
          .whereType<Map>()
          .map(
            (item) => ResumeSkillItem.fromJson(Map<String, dynamic>.from(item)),
          )
          .where((item) => item.resumeVersionId == versionId)
          .toList();

      skills.sort((first, second) {
        final order = first.sortOrder.compareTo(second.sortOrder);

        if (order != 0) {
          return order;
        }

        return first.skill.name.toLowerCase().compareTo(
          second.skill.name.toLowerCase(),
        );
      });

      return skills;
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ============================================================
  // 3. Add a single existing skill
  // ============================================================

  Future<ResumeSkillItem> addSkill({
    required int resumeId,
    required int skillId,
    String? level,
    int sortOrder = 0,
  }) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumeSkills(resumeId),
        data: {'skill_id': skillId, 'level': level, 'sort_order': sortOrder},
      );

      final root = _asMap(response.data);

      return ResumeSkillItem.fromJson(_asMap(root['data']));
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ============================================================
  // 4. Add multiple existing skills in one request
  // ============================================================

  Future<List<ResumeSkillItem>> addSkillsBulk({
    required int resumeId,
    required List<Map<String, dynamic>> skills,
  }) async {
    if (skills.isEmpty || skills.length > 50) {
      throw ArgumentError('You must select between 1 and 50 skills.');
    }

    final skillIds = <int>{};

    for (final skill in skills) {
      final skillId = skill['skill_id'];

      if (skillId is! int || skillId <= 0) {
        throw ArgumentError('Each skill must contain a valid skill_id.');
      }

      if (!skillIds.add(skillId)) {
        throw ArgumentError('Duplicate skill IDs are not allowed.');
      }
    }

    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumeSkillsBulk(resumeId),
        data: {'skills': skills},
      );

      final root = _asMap(response.data);
      final data = root['data'];

      if (data is! List) {
        throw const FormatException('Invalid bulk skills response.');
      }

      return data
          .whereType<Map>()
          .map(
            (item) => ResumeSkillItem.fromJson(Map<String, dynamic>.from(item)),
          )
          .toList();
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ============================================================
  // 5. Create and add a custom skill
  // ============================================================

  Future<ResumeSkillItem> addCustomSkill({
    required int resumeId,
    required String name,
    String? category,
    String? level,
    int sortOrder = 0,
  }) async {
    final normalizedName = name.trim();

    if (normalizedName.isEmpty) {
      throw ArgumentError('Skill name is required.');
    }

    if (normalizedName.length > 100) {
      throw ArgumentError('Skill name cannot exceed 100 characters.');
    }

    final normalizedCategory = category?.trim();

    if (normalizedCategory != null && normalizedCategory.length > 50) {
      throw ArgumentError('Skill category cannot exceed 50 characters.');
    }

    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.resumeSkillsCustom(resumeId),
        data: {
          'name': normalizedName,
          'category': normalizedCategory == null || normalizedCategory.isEmpty
              ? null
              : normalizedCategory,
          'level': level,
          'sort_order': sortOrder,
        },
      );

      final root = _asMap(response.data);

      return ResumeSkillItem.fromJson(_asMap(root['data']));
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ============================================================
  // 6. Update an existing resume skill
  // ============================================================

  Future<ResumeSkillItem> updateSkill({
    required int resumeId,
    required int resumeSkillId,
    String? level,
    int? sortOrder,
  }) async {
    try {
      final data = <String, dynamic>{'level': level};

      if (sortOrder != null) {
        data['sort_order'] = sortOrder;
      }

      final response = await apiClient.dio.put(
        ApiEndpoints.resumeSkill(resumeId, resumeSkillId),
        data: data,
      );

      final root = _asMap(response.data);

      return ResumeSkillItem.fromJson(_asMap(root['data']));
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  // ============================================================
  // 7. Delete a resume skill
  // ============================================================

  Future<void> deleteSkill({
    required int resumeId,
    required int resumeSkillId,
  }) async {
    try {
      await apiClient.dio.delete(
        ApiEndpoints.resumeSkill(resumeId, resumeSkillId),
      );
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }
}
