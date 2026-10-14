class ResumeVersionModel {
  final int id;
  final int resumeId;
  final int versionNumber;
  final String status;
  final String? summary;
  final String template;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const ResumeVersionModel({
    required this.id,
    required this.resumeId,
    required this.versionNumber,
    required this.status,
    required this.summary,
    required this.template,
    this.createdAt,
    this.updatedAt,
  });

  bool get isActive {
    return status.toLowerCase() == 'active';
  }

  bool get hasSummary {
    return summary != null && summary!.trim().isNotEmpty;
  }

  factory ResumeVersionModel.fromJson(Map<String, dynamic> json) {
    return ResumeVersionModel(
      id: _asInt(json['id']),
      resumeId: _asInt(json['resume_id']),
      versionNumber: _asInt(json['version_number']),
      status: json['status']?.toString() ?? 'draft',
      summary: json['summary']?.toString(),
      template: json['template']?.toString() ?? 'classic',
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

  static DateTime? _asDateTime(dynamic value) {
    if (value == null) {
      return null;
    }

    return DateTime.tryParse(value.toString());
  }
}
