import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../models/resume_skill_item.dart';
import '../models/skill_option.dart';
import '../providers/skill_provider.dart';
class SkillsScreen extends ConsumerStatefulWidget {
  final int resumeId;
  final int versionId;
  const SkillsScreen({
    super.key,
    required this.resumeId,
    required this.versionId,
  });
  @override
  ConsumerState<SkillsScreen> createState() => _SkillsScreenState();
}
class _SkillsScreenState extends ConsumerState<SkillsScreen> {
  bool _loading = true;
  bool _actionLoading = false;
  Object? _error;
  List<SkillOption> _catalog = [];
  List<ResumeSkillItem> _resumeSkills = [];
  @override
  void initState() {
    super.initState();
    Future.microtask(_load);
  }
  Future<void> _load() async {
    if (!mounted) {
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final repository = ref.read(skillRepositoryProvider);
      final results = await Future.wait<dynamic>([
        repository.getReferenceSkills(),
        repository.getResumeSkills(
          resumeId: widget.resumeId,
          versionId: widget.versionId,
        ),
      ]);
      if (!mounted) {
        return;
      }
      setState(() {
        _catalog = results[0] as List<SkillOption>;
        _resumeSkills = results[1] as List<ResumeSkillItem>;
        _loading = false;
      });
    } catch (error) {
      if (!mounted) {
        return;
      }
      setState(() {
        _loading = false;
        _error = error;
      });
    }
  }
  String _levelLabel(BuildContext context, String? level) {
    switch (level?.toLowerCase()) {
      case 'beginner':
        return context.l10n.skillLevelBeginner;
      case 'intermediate':
        return context.l10n.skillLevelIntermediate;
      case 'advanced':
        return context.l10n.skillLevelAdvanced;
      case 'expert':
        return context.l10n.skillLevelExpert;
      default:
        return context.l10n.skillLevelNotSpecified;
    }
  }
  Color _levelColor(String? level) {
    switch (level?.toLowerCase()) {
      case 'expert':
        return AppColors.green;
      case 'advanced':
        return AppColors.primary;
      case 'intermediate':
        return AppColors.orange;
      default:
        return AppColors.textMuted;
    }
  }
  // The labels below are kept local to this feature until the next ARB update.
  String _newLabel(BuildContext context, String ar, String en, String fr) {
    switch (Localizations.localeOf(context).languageCode) {
      case 'ar': return ar;
      case 'fr': return fr;
      default: return en;
    }
  }

  String _categoryName(String? category) {
    final value = category?.trim();
    return value == null || value.isEmpty ? 'Other' : value;
  }

  Future<void> _showAddSkill() async {
    if (_actionLoading) return;
    final used = _resumeSkills.map((e) => e.skillId).toSet();
    final available = _catalog.where((e) => !used.contains(e.id)).toList();
    final search = TextEditingController();
    final selected = <int, String?>{};
    String selectedCategory = '__all__';
    String query = '';
    bool submitting = false;

    try {
      final result = await showModalBottomSheet<List<Map<String, dynamic>>>(
        context: context,
        isScrollControlled: true,
        backgroundColor: Colors.transparent,
        builder: (sheetContext) => StatefulBuilder(
          builder: (context, setSheetState) {
            final categories = available.map((e) => _categoryName(e.category))
                .toSet().toList()..sort();
            final filtered = available.where((skill) {
              final matchesCategory = selectedCategory == '__all__' ||
                  _categoryName(skill.category) == selectedCategory;
              final value = query.trim().toLowerCase();
              return matchesCategory && (value.isEmpty ||
                  skill.name.toLowerCase().contains(value) ||
                  _categoryName(skill.category).toLowerCase().contains(value));
            }).toList();

            return FractionallySizedBox(
              heightFactor: 0.88,
              child: Container(
                decoration: const BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.vertical(top: Radius.circular(30)),
                ),
                child: Column(children: [
                  const SizedBox(height: 10),
                  Container(width: 42, height: 4, decoration: BoxDecoration(
                    color: AppColors.surfaceStrong,
                    borderRadius: BorderRadius.circular(999),
                  )),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(20, 18, 20, 10),
                    child: Row(children: [
                      Container(width: 46, height: 46,
                        decoration: BoxDecoration(color: AppColors.primarySoft,
                          borderRadius: BorderRadius.circular(15)),
                        child: const Icon(Icons.psychology_outlined,
                          color: AppColors.primary)),
                      const SizedBox(width: 13),
                      Expanded(child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(context.l10n.selectSkill,
                            style: const TextStyle(fontSize: 20,
                              fontWeight: FontWeight.w800)),
                          const SizedBox(height: 3),
                          Text(_newLabel(context,
                            'اختر عدة مهارات وحدد مستوى كل مهارة',
                            'Choose multiple skills and set each level',
                            'Choisissez plusieurs compétences et leurs niveaux'),
                            style: const TextStyle(fontSize: 11.5,
                              color: AppColors.textSecondary)),
                        ],
                      )),
                    ]),
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: TextField(
                      controller: search,
                      onChanged: (value) => setSheetState(() => query = value),
                      decoration: InputDecoration(
                        hintText: context.l10n.searchSkillsHint,
                        prefixIcon: const Icon(Icons.search_rounded),
                        filled: true, fillColor: AppColors.surfaceSoft,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: BorderSide.none),
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(height: 39, child: ListView(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    children: [
                      Padding(
                        padding: const EdgeInsetsDirectional.only(end: 8),
                        child: ChoiceChip(
                          label: Text(context.l10n.allCategories),
                          selected: selectedCategory == '__all__',
                          onSelected: (_) => setSheetState(
                            () => selectedCategory = '__all__'),
                        ),
                      ),
                      ...categories.map((category) => Padding(
                        padding: const EdgeInsetsDirectional.only(end: 8),
                        child: ChoiceChip(
                          label: Text(category),
                          selected: selectedCategory == category,
                          onSelected: (_) => setSheetState(
                            () => selectedCategory = category),
                        ),
                      )),
                    ],
                  )),
                  const SizedBox(height: 8),
                  Expanded(child: filtered.isEmpty
                    ? Center(child: Text(context.l10n.noMatchingSkills))
                    : ListView.separated(
                        padding: const EdgeInsets.fromLTRB(20, 6, 20, 10),
                        itemCount: filtered.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 8),
                        itemBuilder: (context, index) {
                          final skill = filtered[index];
                          final isSelected = selected.containsKey(skill.id);
                          return Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: isSelected ? AppColors.primarySoft : AppColors.surfaceSoft,
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                CheckboxListTile(
                                  contentPadding: EdgeInsets.zero,
                                  value: isSelected,
                                  title: Text(skill.name,
                                    style: const TextStyle(fontWeight: FontWeight.w700)),
                                  subtitle: Text(_categoryName(skill.category)),
                                  controlAffinity: ListTileControlAffinity.leading,
                                  onChanged: submitting ? null : (checked) {
                                    setSheetState(() {
                                      if (checked == true) {
                                        if (selected.length >= 50) return;
                                        selected[skill.id] = null;
                                      } else {
                                        selected.remove(skill.id);
                                      }
                                    });
                                  },
                                ),
                                if (isSelected) Padding(
                                  padding: const EdgeInsetsDirectional.only(start: 12),
                                  child: Wrap(spacing: 6, runSpacing: 5, children: [
                                    for (final level in <String?>[
                                      null, 'beginner', 'intermediate', 'advanced', 'expert',
                                    ])
                                      _LevelChoice(
                                        label: _levelLabel(context, level),
                                        selected: selected[skill.id] == level,
                                        onTap: () => setSheetState(
                                          () => selected[skill.id] = level),
                                      ),
                                  ]),
                                ),
                              ],
                            ),
                          );
                        },
                      )),
                  Container(
                    padding: EdgeInsets.fromLTRB(20, 12, 20,
                      MediaQuery.of(sheetContext).padding.bottom + 18),
                    decoration: const BoxDecoration(color: AppColors.surface,
                      border: Border(top: BorderSide(color: AppColors.border))),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(_newLabel(context,
                          'المهارات المحددة: ${selected.length} من 50',
                          'Selected: ${selected.length} of 50',
                          'Sélectionnées : ${selected.length} sur 50'),
                          style: const TextStyle(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 10),
                        FilledButton.icon(
                          onPressed: submitting || selected.isEmpty ? null : () {
                            setSheetState(() => submitting = true);
                            Navigator.of(sheetContext).pop([
                              for (final entry in selected.entries)
                                <String, dynamic>{
                                  'skill_id': entry.key,
                                  'level': entry.value,
                                  'sort_order': available.indexWhere(
                                    (s) => s.id == entry.key),
                                },
                            ]);
                          },
                          icon: const Icon(Icons.add_rounded),
                          label: Text(_newLabel(context, 'إضافة المهارات المحددة',
                            'Add selected skills', 'Ajouter les compétences')),
                        ),
                      ],
                    ),
                  ),
                ]),
              ),
            );
          },
        ),
      );
      if (result == null || result.isEmpty || !mounted) return;
      setState(() => _actionLoading = true);
      try {
        final repository = ref.read(skillRepositoryProvider);
        if (result.length == 1) {
          await repository.addSkill(
            resumeId: widget.resumeId,
            skillId: result.first['skill_id'] as int,
            level: result.first['level'] as String?,
          );
        } else {
          await repository.addSkillsBulk(resumeId: widget.resumeId, skills: result);
        }
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          behavior: SnackBarBehavior.floating,
          content: Text(context.l10n.skillAdded),
        ));
        await _load();
      } catch (error) {
        if (!mounted) return;
        final message = error.toString().toLowerCase();
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text(message.contains('already')
            ? context.l10n.skillAlreadyAdded
            : localizeErrorMessage(context, error)),
        ));
      } finally {
        if (mounted) setState(() => _actionLoading = false);
      }
    } finally {
      search.dispose();
    }
  }

  Future<void> _showCustomSkill() async {
    if (_actionLoading) return;
    final name = TextEditingController();
    final category = TextEditingController();
    String? level;
    final categories = _catalog.map((e) => e.category?.trim())
        .whereType<String>().where((e) => e.isNotEmpty).toSet().toList()..sort();
    try {
      final result = await showModalBottomSheet<Map<String, String?>>(
        context: context,
        isScrollControlled: true,
        backgroundColor: Colors.transparent,
        builder: (sheetContext) => StatefulBuilder(
          builder: (context, setSheetState) => Padding(
            padding: EdgeInsets.only(bottom: MediaQuery.of(sheetContext).viewInsets.bottom),
            child: Container(
              padding: EdgeInsets.fromLTRB(20, 18, 20,
                MediaQuery.of(sheetContext).padding.bottom + 20),
              decoration: const BoxDecoration(color: AppColors.surface,
                borderRadius: BorderRadius.vertical(top: Radius.circular(28))),
              child: SingleChildScrollView(child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(child: Container(width: 42, height: 4,
                    decoration: BoxDecoration(color: AppColors.surfaceStrong,
                      borderRadius: BorderRadius.circular(999)))),
                  const SizedBox(height: 20),
                  Text(_newLabel(context, 'إضافة مهارة جديدة',
                      'Create a custom skill', 'Créer une compétence'),
                    style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 12),
                  TextField(controller: name, maxLength: 100,
                    decoration: InputDecoration(
                      labelText: _newLabel(context, 'اسم المهارة', 'Skill name',
                        'Nom de la compétence'),
                      border: const OutlineInputBorder())),
                  const SizedBox(height: 8),
                  TextField(controller: category, maxLength: 50,
                    decoration: InputDecoration(
                      labelText: _newLabel(context, 'التصنيف', 'Category', 'Catégorie'),
                      border: const OutlineInputBorder())),
                  if (categories.isNotEmpty) ...[
                    const SizedBox(height: 6),
                    Text(_newLabel(context, 'اختر تصنيفًا موجودًا أو اكتبه بنفسك',
                      'Pick a category or type your own',
                      'Choisir une catégorie ou la saisir')),
                    const SizedBox(height: 6),
                    SizedBox(height: 38, child: ListView(
                      scrollDirection: Axis.horizontal,
                      children: [for (final c in categories) Padding(
                        padding: const EdgeInsetsDirectional.only(end: 6),
                        child: ActionChip(label: Text(c),
                          onPressed: () => category.text = c),
                      )],
                    )),
                  ],
                  const SizedBox(height: 14),
                  Text(context.l10n.chooseSkillLevel,
                    style: const TextStyle(fontWeight: FontWeight.w700)),
                  const SizedBox(height: 8),
                  Wrap(spacing: 7, runSpacing: 7, children: [
                    for (final value in <String?>[
                      null, 'beginner', 'intermediate', 'advanced', 'expert',
                    ])
                      _LevelChoice(
                        label: _levelLabel(context, value),
                        selected: level == value,
                        onTap: () => setSheetState(() => level = value),
                      ),
                  ]),
                  const SizedBox(height: 18),
                  FilledButton.icon(
                    onPressed: () {
                      if (name.text.trim().isEmpty) return;
                      Navigator.of(sheetContext).pop({
                        'name': name.text.trim(),
                        'category': category.text.trim(),
                        'level': level,
                      });
                    },
                    icon: const Icon(Icons.add_rounded),
                    label: Text(_newLabel(context, 'حفظ وإضافة المهارة',
                      'Save and add skill', 'Enregistrer et ajouter')),
                  ),
                ],
              )),
            ),
          ),
        ),
      );
      if (result == null || !mounted) return;
      setState(() => _actionLoading = true);
      try {
        await ref.read(skillRepositoryProvider).addCustomSkill(
          resumeId: widget.resumeId,
          name: result['name']!,
          category: result['category'],
          level: result['level'],
        );
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          behavior: SnackBarBehavior.floating,
          content: Text(context.l10n.skillAdded),
        ));
        await _load();
      } catch (error) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text(localizeErrorMessage(context, error)),
        ));
      } finally {
        if (mounted) setState(() => _actionLoading = false);
      }
    } finally {
      name.dispose();
      category.dispose();
    }
  }

  Future<void> _showSkillActions() async {
    await showModalBottomSheet<void>(
      context: context,
      backgroundColor: AppColors.surface,
      showDragHandle: true,
      builder: (sheetContext) => SafeArea(child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          Text(context.l10n.addSkill,
            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          const SizedBox(height: 14),
          FilledButton.icon(
            onPressed: () {
              Navigator.pop(sheetContext);
              _showAddSkill();
            },
            icon: const Icon(Icons.checklist_rounded),
            label: Text(_newLabel(context, 'اختيار مهارات من القائمة',
              'Select skills from catalog', 'Choisir dans le catalogue')),
          ),
          const SizedBox(height: 10),
          OutlinedButton.icon(
            onPressed: () {
              Navigator.pop(sheetContext);
              _showCustomSkill();
            },
            icon: const Icon(Icons.edit_note_rounded),
            label: Text(_newLabel(context, 'إضافة مهارة غير موجودة',
              'Create a new skill', 'Créer une compétence')),
          ),
        ]),
      )),
    );
  }

  Future<void> _editLevel(ResumeSkillItem item) async {
    const none = '__none__';
    String selected = item.level?.toLowerCase() ?? none;
    final result = await showModalBottomSheet<String>(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) {
        return StatefulBuilder(
          builder: (context, setSheetState) {
            return Container(
              padding: EdgeInsets.fromLTRB(
                20,
                14,
                20,
                MediaQuery.of(sheetContext).padding.bottom + 24,
              ),
              decoration: const BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 42,
                      height: 4,
                      decoration: BoxDecoration(
                        color: AppColors.surfaceStrong,
                        borderRadius: BorderRadius.circular(999),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    context.l10n.editSkillLevel,
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    item.skill.name,
                    style: const TextStyle(color: AppColors.textSecondary),
                  ),
                  const SizedBox(height: 18),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      _LevelChoice(
                        label: context.l10n.skillLevelNotSpecified,
                        selected: selected == none,
                        onTap: () {
                          setSheetState(() {
                            selected = none;
                          });
                        },
                      ),
                      _LevelChoice(
                        label: context.l10n.skillLevelBeginner,
                        selected: selected == 'beginner',
                        onTap: () {
                          setSheetState(() {
                            selected = 'beginner';
                          });
                        },
                      ),
                      _LevelChoice(
                        label: context.l10n.skillLevelIntermediate,
                        selected: selected == 'intermediate',
                        onTap: () {
                          setSheetState(() {
                            selected = 'intermediate';
                          });
                        },
                      ),
                      _LevelChoice(
                        label: context.l10n.skillLevelAdvanced,
                        selected: selected == 'advanced',
                        onTap: () {
                          setSheetState(() {
                            selected = 'advanced';
                          });
                        },
                      ),
                      _LevelChoice(
                        label: context.l10n.skillLevelExpert,
                        selected: selected == 'expert',
                        onTap: () {
                          setSheetState(() {
                            selected = 'expert';
                          });
                        },
                      ),
                    ],
                  ),
                  const SizedBox(height: 18),
                  FilledButton(
                    onPressed: () {
                      Navigator.of(sheetContext).pop(selected);
                    },
                    child: Text(context.l10n.saveSkillLevel),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
    if (result == null || !mounted) {
      return;
    }
    setState(() {
      _actionLoading = true;
    });
    try {
      await ref
          .read(skillRepositoryProvider)
          .updateSkill(
            resumeId: widget.resumeId,
            resumeSkillId: item.id,
            level: result == none ? null : result,
            sortOrder: item.sortOrder,
          );
      if (!mounted) {
        return;
      }
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          behavior: SnackBarBehavior.floating,
          content: Text(context.l10n.skillUpdated),
        ),
      );
      await _load();
    } catch (error) {
      if (!mounted) {
        return;
      }
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(localizeErrorMessage(context, error))),
      );
    } finally {
      if (mounted) {
        setState(() {
          _actionLoading = false;
        });
      }
    }
  }
  Future<void> _deleteSkill(ResumeSkillItem item) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (dialogContext) {
        return AlertDialog(
          title: Text(context.l10n.deleteSkillTitle),
          content: Text(context.l10n.deleteSkillMessage(item.skill.name)),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(dialogContext).pop(false);
              },
              child: Text(context.l10n.cancel),
            ),
            TextButton(
              onPressed: () {
                Navigator.of(dialogContext).pop(true);
              },
              style: TextButton.styleFrom(foregroundColor: AppColors.red),
              child: Text(context.l10n.delete),
            ),
          ],
        );
      },
    );
    if (confirmed != true) {
      return;
    }
    try {
      await ref
          .read(skillRepositoryProvider)
          .deleteSkill(resumeId: widget.resumeId, resumeSkillId: item.id);
      if (!mounted) {
        return;
      }
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          behavior: SnackBarBehavior.floating,
          content: Text(context.l10n.skillDeleted),
        ),
      );
      await _load();
    } catch (error) {
      if (!mounted) {
        return;
      }
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(localizeErrorMessage(context, error))),
      );
    }
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 18, 20, 12),
                child: Row(
                  children: [
                    Container(
                      width: 43,
                      height: 43,
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: IconButton(
                        onPressed: () {
                          context.pop();
                        },
                        icon: const Icon(Icons.arrow_back_rounded, size: 19),
                      ),
                    ),
                    const Spacer(),
                    const AppLanguageSwitcher(),
                  ],
                ),
              ),
              Expanded(child: _buildBody(context)),
            ],
          ),
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _actionLoading ? null : _showSkillActions,
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: _actionLoading
            ? const SizedBox(
                width: 18,
                height: 18,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  color: Colors.white,
                ),
              )
            : const Icon(Icons.add_rounded),
        label: Text(
          context.l10n.addSkill,
          style: const TextStyle(fontWeight: FontWeight.w700),
        ),
      ),
    );
  }
  // Visual grouping uses the existing skill category (no new backend schema).
  List<Widget> _buildGroupedSkills(BuildContext context) {
    final groups = <String, List<ResumeSkillItem>>{};
    for (final item in _resumeSkills) {
      groups.putIfAbsent(_categoryName(item.skill.category), () => []).add(item);
    }
    final keys = groups.keys.toList()..sort();
    return [
      for (final category in keys) ...[
        Padding(
          padding: const EdgeInsets.only(bottom: 12, top: 6),
          child: Row(children: [
            const Icon(Icons.folder_outlined, color: AppColors.primary, size: 20),
            const SizedBox(width: 8),
            Expanded(child: Text(category,
              style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800))),
            Text('${groups[category]!.length}',
              style: const TextStyle(color: AppColors.textSecondary)),
          ]),
        ),
        for (final item in groups[category]!)
          Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: _SkillCard(
              item: item,
              levelLabel: _levelLabel(context, item.level),
              levelColor: _levelColor(item.level),
              onEdit: () => _editLevel(item),
              onDelete: () => _deleteSkill(item),
            ),
          ),
      ],
    ];
  }

  Widget _buildBody(BuildContext context) {
    if (_loading) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.primary),
      );
    }
    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(30),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.error_outline_rounded,
                size: 38,
                color: AppColors.red,
              ),
              const SizedBox(height: 12),
              Text(
                localizeErrorMessage(context, _error),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 16),
              FilledButton(
                onPressed: _load,
                child: Text(context.l10n.tryAgain),
              ),
            ],
          ),
        ),
      );
    }
    return RefreshIndicator(
      color: AppColors.primary,
      onRefresh: _load,
      child: ListView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(20, 18, 20, 110),
        children: [
          Text(
            context.l10n.skillsEyebrow,
            style: const TextStyle(
              color: AppColors.primary,
              fontSize: 10,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            context.l10n.skillsScreenTitle,
            style: Theme.of(context).textTheme.headlineLarge,
          ),
          const SizedBox(height: 11),
          Text(
            context.l10n.skillsScreenSubtitle,
            style: const TextStyle(
              fontSize: 14,
              height: 1.55,
              color: AppColors.textSecondary,
            ),
          ),
          const SizedBox(height: 22),
          Container(
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(
              color: AppColors.greenSoft,
              borderRadius: BorderRadius.circular(17),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(
                  Icons.verified_outlined,
                  color: AppColors.green,
                  size: 18,
                ),
                const SizedBox(width: 9),
                Expanded(
                  child: Text(
                    context.l10n.skillsTruthNotice,
                    style: const TextStyle(
                      fontSize: 12,
                      height: 1.5,
                      color: AppColors.textSecondary,
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),
          if (_resumeSkills.isEmpty)
            _EmptySkills(onAdd: _showSkillActions)
          else
            ..._buildGroupedSkills(context),
        ],
      ),
    );
  }
}
class _SkillCard extends StatelessWidget {
  final ResumeSkillItem item;
  final String levelLabel;
  final Color levelColor;
  final VoidCallback onEdit;
  final VoidCallback onDelete;
  const _SkillCard({
    required this.item,
    required this.levelLabel,
    required this.levelColor,
    required this.onEdit,
    required this.onDelete,
  });
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(17),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(21),
        border: Border.all(color: AppColors.border),
        boxShadow: const [
          BoxShadow(
            color: AppColors.shadow,
            blurRadius: 20,
            offset: Offset(0, 7),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: AppColors.primarySoft,
              borderRadius: BorderRadius.circular(15),
            ),
            child: const Icon(
              Icons.psychology_outlined,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(width: 13),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.skill.name,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                if (item.skill.category != null &&
                    item.skill.category!.trim().isNotEmpty) ...[
                  const SizedBox(height: 4),
                  Text(
                    item.skill.category!,
                    style: const TextStyle(
                      fontSize: 10.5,
                      color: AppColors.textMuted,
                    ),
                  ),
                ],
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 9,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: levelColor.withValues(alpha: 0.10),
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    levelLabel,
                    style: TextStyle(
                      fontSize: 9.5,
                      fontWeight: FontWeight.w800,
                      color: levelColor,
                    ),
                  ),
                ),
              ],
            ),
          ),
          PopupMenuButton<String>(
            onSelected: (value) {
              if (value == 'edit') {
                onEdit();
              }
              if (value == 'delete') {
                onDelete();
              }
            },
            itemBuilder: (context) {
              return [
                PopupMenuItem(
                  value: 'edit',
                  child: Text(context.l10n.editSkillLevel),
                ),
                PopupMenuItem(
                  value: 'delete',
                  child: Text(
                    context.l10n.delete,
                    style: const TextStyle(color: AppColors.red),
                  ),
                ),
              ];
            },
          ),
        ],
      ),
    );
  }
}
class _LevelChoice extends StatelessWidget {
  final String label;
  final bool selected;
  final VoidCallback onTap;
  const _LevelChoice({
    required this.label,
    required this.selected,
    required this.onTap,
  });
  @override
  Widget build(BuildContext context) {
    return ChoiceChip(
      label: Text(label),
      selected: selected,
      onSelected: (_) {
        onTap();
      },
      selectedColor: AppColors.primarySoft,
      side: BorderSide(color: selected ? AppColors.primary : AppColors.border),
      labelStyle: TextStyle(
        color: selected ? AppColors.primary : AppColors.textSecondary,
        fontWeight: selected ? FontWeight.w700 : FontWeight.w500,
      ),
    );
  }
}
class _EmptySkills extends StatelessWidget {
  final VoidCallback onAdd;
  const _EmptySkills({required this.onAdd});
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          Container(
            width: 76,
            height: 76,
            decoration: const BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.primarySoft,
            ),
            child: const Icon(
              Icons.psychology_outlined,
              size: 32,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(height: 20),
          Text(
            context.l10n.noSkillsTitle,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 8),
          Text(
            context.l10n.noSkillsSubtitle,
            textAlign: TextAlign.center,
            style: const TextStyle(
              height: 1.55,
              color: AppColors.textSecondary,
            ),
          ),
          const SizedBox(height: 20),
          FilledButton.icon(
            onPressed: onAdd,
            icon: const Icon(Icons.add_rounded),
            label: Text(context.l10n.addSkill),
          ),
        ],
      ),
    );
  }
}
