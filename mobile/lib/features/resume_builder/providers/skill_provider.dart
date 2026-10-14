import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/providers/auth_provider.dart';
import '../data/skill_repository.dart';

final skillRepositoryProvider = Provider<SkillRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);

  return SkillRepository(apiClient: apiClient);
});
