import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/providers/auth_provider.dart';
import '../../resume_builder/models/resume_detail.dart';
import '../data/resume_repository.dart';
import '../models/resume_item.dart';

final resumeRepositoryProvider = Provider<ResumeRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);

  return ResumeRepository(apiClient: apiClient);
});

final resumesProvider = FutureProvider<List<ResumeItem>>((ref) async {
  final repository = ref.watch(resumeRepositoryProvider);

  return repository.getResumes();
});

final resumeDetailProvider = FutureProvider.family<ResumeDetail, int>((
  ref,
  resumeId,
) async {
  final repository = ref.watch(resumeRepositoryProvider);

  return repository.getResume(resumeId);
});
