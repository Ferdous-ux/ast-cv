class ResumeEducation {
  final int id;
  final int resumeVersionId;
  final String institution;
  final String? degree;
  final String? fieldOfStudy;
  final String? location;
  final DateTime? startDate;
  final DateTime? endDate;
  final String? description;
  final int sortOrder;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const ResumeEducation({
    required this.id,
    required this.resumeVersionId,
    required this.institution,
    required this.degree,
    required this.fieldOfStudy,
    required this.location,
    required this.startDate,
    required this.endDate,
    required this.description,
    required this.sortOrder,
    this.createdAt,
    this.updatedAt,
  });

  bool get hasDegree {
    return degree != null && degree!.trim().isNotEmpty;
  }

  bool get hasFieldOfStudy {
    return fieldOfStudy != null && fieldOfStudy!.trim().isNotEmpty;
  }

  bool get hasDescription {
    return description != null && description!.trim().isNotEmpty;
  }

  factory ResumeEducation.fromJson(Map<String, dynamic> json) {
    return ResumeEducation(
      id: _asInt(json['id']),
      resumeVersionId: _asInt(json['resume_version_id']),
      institution: json['institution']?.toString() ?? '',
      degree: json['degree']?.toString(),
      fieldOfStudy: json['field_of_study']?.toString(),
      location: json['location']?.toString(),
      startDate: _asDate(json['start_date']),
      endDate: _asDate(json['end_date']),
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
