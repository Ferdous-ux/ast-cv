import 'dart:convert';
import 'dart:io';

Future<void> main() async {
  final translations = <String, Map<String, dynamic>>{
    'en': {
      'skillsEyebrow': 'SKILLS',
      'skillsScreenTitle': 'Your professional skills.',
      'skillsScreenSubtitle':
          'Choose genuine skills from the AST-CV catalog and optionally specify your actual proficiency.',
      'addSkill': 'Add skill',
      'selectSkill': 'Select a skill',
      'selectSkillSubtitle':
          'Search the catalog and choose a skill you genuinely have.',
      'searchSkillsHint': 'Search skills or categories...',
      'allCategories': 'All',
      'noMatchingSkills': 'No matching skills found.',
      'noMoreSkillsAvailable': 'All available skills have already been added.',
      'chooseSkillLevel': 'Proficiency level',
      'skillLevelNotSpecified': 'Not specified',
      'skillLevelBeginner': 'Beginner',
      'skillLevelIntermediate': 'Intermediate',
      'skillLevelAdvanced': 'Advanced',
      'skillLevelExpert': 'Expert',
      'addSelectedSkill': 'Add selected skill',
      'skillAdded': 'Skill added successfully.',
      'skillUpdated': 'Skill level updated successfully.',
      'skillDeleted': 'Skill removed successfully.',
      'skillAlreadyAdded':
          'This skill is already in the current resume version.',
      'editSkillLevel': 'Edit proficiency level',
      'saveSkillLevel': 'Save level',
      'deleteSkillTitle': 'Remove skill?',
      'deleteSkillMessage': 'Remove “{skill}” from this resume?',
      '@deleteSkillMessage': {
        'placeholders': {
          'skill': {'type': 'String'},
        },
      },
      'noSkillsTitle': 'No skills added yet',
      'noSkillsSubtitle':
          'Add skills that genuinely represent your abilities and experience.',
      'skillsTruthNotice':
          'Only add skills you actually have. AST-CV uses these skills for ATS matching and will not invent abilities for you.',
    },

    'ar': {
      'skillsEyebrow': 'المهارات',
      'skillsScreenTitle': 'مهاراتك المهنية.',
      'skillsScreenSubtitle':
          'اختر مهاراتك الحقيقية من قائمة AST-CV وحدد مستوى إتقانك الفعلي عند الحاجة.',
      'addSkill': 'إضافة مهارة',
      'selectSkill': 'اختر مهارة',
      'selectSkillSubtitle':
          'ابحث في قائمة المهارات واختر مهارة تمتلكها فعليًا.',
      'searchSkillsHint': 'ابحث عن مهارة أو تصنيف...',
      'allCategories': 'الكل',
      'noMatchingSkills': 'لم يتم العثور على مهارات مطابقة.',
      'noMoreSkillsAvailable': 'تمت إضافة جميع المهارات المتاحة.',
      'chooseSkillLevel': 'مستوى الإتقان',
      'skillLevelNotSpecified': 'غير محدد',
      'skillLevelBeginner': 'مبتدئ',
      'skillLevelIntermediate': 'متوسط',
      'skillLevelAdvanced': 'متقدم',
      'skillLevelExpert': 'خبير',
      'addSelectedSkill': 'إضافة المهارة',
      'skillAdded': 'تمت إضافة المهارة بنجاح.',
      'skillUpdated': 'تم تحديث مستوى المهارة بنجاح.',
      'skillDeleted': 'تم حذف المهارة بنجاح.',
      'skillAlreadyAdded': 'هذه المهارة مضافة بالفعل إلى النسخة الحالية.',
      'editSkillLevel': 'تعديل مستوى الإتقان',
      'saveSkillLevel': 'حفظ المستوى',
      'deleteSkillTitle': 'حذف المهارة؟',
      'deleteSkillMessage': 'هل تريد حذف «{skill}» من هذه السيرة؟',
      '@deleteSkillMessage': {
        'placeholders': {
          'skill': {'type': 'String'},
        },
      },
      'noSkillsTitle': 'لم تضف أي مهارات بعد',
      'noSkillsSubtitle': 'أضف المهارات التي تمثل قدراتك وخبراتك الحقيقية.',
      'skillsTruthNotice':
          'أضف فقط المهارات التي تمتلكها فعليًا. يستخدم AST-CV هذه المهارات في مطابقة ATS ولن يختلق لك قدرات غير حقيقية.',
    },

    'fr': {
      'skillsEyebrow': 'COMPÉTENCES',
      'skillsScreenTitle': 'Vos compétences professionnelles.',
      'skillsScreenSubtitle':
          'Choisissez vos compétences réelles dans le catalogue AST-CV et indiquez votre véritable niveau si nécessaire.',
      'addSkill': 'Ajouter une compétence',
      'selectSkill': 'Choisir une compétence',
      'selectSkillSubtitle':
          'Recherchez dans le catalogue une compétence que vous possédez réellement.',
      'searchSkillsHint': 'Rechercher une compétence ou catégorie...',
      'allCategories': 'Toutes',
      'noMatchingSkills': 'Aucune compétence correspondante.',
      'noMoreSkillsAvailable':
          'Toutes les compétences disponibles ont déjà été ajoutées.',
      'chooseSkillLevel': 'Niveau de maîtrise',
      'skillLevelNotSpecified': 'Non précisé',
      'skillLevelBeginner': 'Débutant',
      'skillLevelIntermediate': 'Intermédiaire',
      'skillLevelAdvanced': 'Avancé',
      'skillLevelExpert': 'Expert',
      'addSelectedSkill': 'Ajouter la compétence',
      'skillAdded': 'Compétence ajoutée avec succès.',
      'skillUpdated': 'Niveau mis à jour avec succès.',
      'skillDeleted': 'Compétence supprimée avec succès.',
      'skillAlreadyAdded':
          'Cette compétence est déjà ajoutée à la version actuelle.',
      'editSkillLevel': 'Modifier le niveau',
      'saveSkillLevel': 'Enregistrer le niveau',
      'deleteSkillTitle': 'Supprimer la compétence ?',
      'deleteSkillMessage': 'Supprimer « {skill} » de ce CV ?',
      '@deleteSkillMessage': {
        'placeholders': {
          'skill': {'type': 'String'},
        },
      },
      'noSkillsTitle': 'Aucune compétence ajoutée',
      'noSkillsSubtitle':
          'Ajoutez uniquement les compétences qui représentent réellement vos capacités.',
      'skillsTruthNotice':
          'Ajoutez uniquement les compétences que vous possédez réellement. AST-CV les utilise pour la correspondance ATS et n’inventera pas de compétences.',
    },
  };

  for (final locale in translations.keys) {
    final file = File('lib/l10n/app_$locale.arb');

    if (!await file.exists()) {
      stderr.writeln('Missing ${file.path}');

      exitCode = 1;
      return;
    }

    final decoded = jsonDecode(await file.readAsString());

    final map = Map<String, dynamic>.from(decoded as Map);

    map.addAll(translations[locale]!);

    const encoder = JsonEncoder.withIndent('  ');

    await file.writeAsString('${encoder.convert(map)}\n');

    stdout.writeln('Updated ${file.path}');
  }
}
