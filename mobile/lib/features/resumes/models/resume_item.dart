class ResumeVersionPreview {
  final int id;
  final int versionNumber;
  final String status;
  final String? summary;
  final String? template;

  const ResumeVersionPreview({
    required this.id,
    required this.versionNumber,
    required this.status,
    this.summary,
    this.template,
  });

  factory ResumeVersionPreview.fromJson(Map<String, dynamic> json) {
    return ResumeVersionPreview(
      id: _readInt(json['id']),
      versionNumber: _readInt(json['version_number']),
      status: json['status']?.toString() ?? 'draft',
      summary: json['summary']?.toString(),
      template: json['template']?.toString(),
    );
  }

  static int _readInt(dynamic value) {
    if (value is int) {
      return value;
    }

    if (value is num) {
      return value.toInt();
    }

    return int.tryParse(value?.toString() ?? '') ?? 0;
  }
}

class ResumeItem {
  final int id;
  final String title;
  final String status;
  final int? currentVersionId;
  final ResumeVersionPreview? currentVersion;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const ResumeItem({
    required this.id,
    required this.title,
    required this.status,
    this.currentVersionId,
    this.currentVersion,
    this.createdAt,
    this.updatedAt,
  });

  factory ResumeItem.fromJson(Map<String, dynamic> json) {
    ResumeVersionPreview? currentVersion;

    final rawCurrentVersion = json['current_version'];

    if (rawCurrentVersion is Map) {
      currentVersion = ResumeVersionPreview.fromJson(
        Map<String, dynamic>.from(rawCurrentVersion),
      );
    }

    return ResumeItem(
      id: _readInt(json['id']),
      title: json['title']?.toString() ?? 'Untitled Resume',
      status: json['status']?.toString() ?? 'draft',
      currentVersionId: _readNullableInt(json['current_version_id']),
      currentVersion: currentVersion,
      createdAt: _readDate(json['created_at']),
      updatedAt: _readDate(json['updated_at']),
    );
  }

  bool get hasCurrentVersion {
    return currentVersionId != null || currentVersion != null;
  }

  static int _readInt(dynamic value) {
    if (value is int) {
      return value;
    }

    if (value is num) {
      return value.toInt();
    }

    return int.tryParse(value?.toString() ?? '') ?? 0;
  }

  static int? _readNullableInt(dynamic value) {
    if (value == null) {
      return null;
    }

    if (value is int) {
      return value;
    }

    if (value is num) {
      return value.toInt();
    }

    return int.tryParse(value.toString());
  }

  static DateTime? _readDate(dynamic value) {
    if (value == null) {
      return null;
    }

    return DateTime.tryParse(value.toString());
  }
}
