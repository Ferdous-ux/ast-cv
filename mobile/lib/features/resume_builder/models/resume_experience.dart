class ResumeExperience {
  final int id;
  final int resumeVersionId;
  final String companyName;
  final String jobTitle;
  final String? location;
  final DateTime? startDate;
  final DateTime? endDate;
  final bool isCurrent;
  final String? description;
  final int sortOrder;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const ResumeExperience({
    required this.id,
    required this.resumeVersionId,
    required this.companyName,
    required this.jobTitle,
    required this.location,
    required this.startDate,
    required this.endDate,
    required this.isCurrent,
    required this.description,
    required this.sortOrder,
    this.createdAt,
    this.updatedAt,
  });

  bool get hasDescription {
    return description != null && description!.trim().isNotEmpty;
  }

  factory ResumeExperience.fromJson(Map<String, dynamic> json) {
    return ResumeExperience(
      id: _asInt(json['id']),
      resumeVersionId: _asInt(json['resume_version_id']),
      companyName: json['company_name']?.toString() ?? '',
      jobTitle: json['job_title']?.toString() ?? '',
      location: json['location']?.toString(),
      startDate: _asDate(json['start_date']),
      endDate: _asDate(json['end_date']),
      isCurrent: _asBool(json['is_current']),
      description: json['description']?.toString(),
      sortOrder: _asInt(json['sort_order']),
      createdAt: _asDateTime(json['created_at']),
      updatedAt: _asDateTime(json['updated_at']),
    );
  }

  static int _asInt(dynamic value) {
    if (value is int) {
      return value;
    }

    return int.tryParse(value?.toString() ?? '') ?? 0;
  }

  static bool _asBool(dynamic value) {
    if (value is bool) {
      return value;
    }

    if (value is int) {
      return value == 1;
    }

    final normalized = value?.toString().toLowerCase();

    return normalized == '1' || normalized == 'true';
  }

  static DateTime? _asDate(dynamic value) {
    if (value == null) {
      return null;
    }

    return DateTime.tryParse(value.toString());
  }

  static DateTime? _asDateTime(dynamic value) {
    if (value == null) {
      return null;
    }

    return DateTime.tryParse(value.toString());
  }
}
