import 'dart:convert';
import 'dart:io';

Future<void> main() async {
  final translations = <String, Map<String, dynamic>>{
    'en': {
      'experienceEyebrow': 'WORK EXPERIENCE',
      'experienceScreenTitle': 'Your experience.',
      'experienceScreenSubtitle':
          'Add the roles that genuinely represent your professional history and achievements.',
      'addExperience': 'Add experience',
      'noExperienceTitle': 'No experience added yet',
      'noExperienceSubtitle':
          'Add your real work history, internships or relevant professional roles.',
      'edit': 'Edit',
      'deleteExperienceTitle': 'Delete experience?',
      'deleteExperienceMessage': 'Delete “{jobTitle}” at “{company}”?',
      '@deleteExperienceMessage': {
        'placeholders': {
          'jobTitle': {'type': 'String'},
          'company': {'type': 'String'},
        },
      },
      'experienceDeleted': 'Experience deleted successfully.',
      'newExperienceTitle': 'Add work experience.',
      'editExperienceTitle': 'Edit work experience.',
      'experienceEditorSubtitle':
          'Keep the information accurate, clear and focused on real responsibilities and achievements.',
      'jobTitleLabel': 'Job title',
      'jobTitleHint': 'e.g. Flutter Developer',
      'companyNameLabel': 'Company',
      'companyNameHint': 'e.g. AST Technologies',
      'locationLabel': 'Location',
      'locationHint': 'e.g. Sana’a, Yemen',
      'startDateLabel': 'Start date',
      'endDateLabel': 'End date',
      'selectDate': 'Select date',
      'dateNotSpecified': 'Not specified',
      'present': 'Present',
      'currentlyWorkingHere': 'I currently work here',
      'currentlyWorkingHereSubtitle':
          'No end date will be saved for this role.',
      'currentPosition': 'CURRENT POSITION',
      'experienceDescriptionLabel': 'Responsibilities and achievements',
      'experienceDescriptionHint':
          'Describe your actual responsibilities, outcomes and measurable achievements...',
      'createExperience': 'Add experience',
      'experienceCreated': 'Experience added successfully.',
      'experienceUpdated': 'Experience updated successfully.',
      'companyNameRequired': 'Company name is required.',
      'jobTitleRequired': 'Job title is required.',
      'maximum150Characters': 'Maximum 150 characters.',
      'maximum200Characters': 'Maximum 200 characters.',
      'endDateBeforeStart': 'End date cannot be before the start date.',
      'experienceTruthNotice':
          'Use only real responsibilities and achievements. AST-CV will never invent work experience.',
    },

    'ar': {
      'experienceEyebrow': 'الخبرات العملية',
      'experienceScreenTitle': 'خبراتك المهنية.',
      'experienceScreenSubtitle':
          'أضف الوظائف والخبرات التي تمثل تاريخك المهني الحقيقي وإنجازاتك.',
      'addExperience': 'إضافة خبرة',
      'noExperienceTitle': 'لم تضف أي خبرة بعد',
      'noExperienceSubtitle':
          'أضف تاريخك الوظيفي الحقيقي أو التدريب أو الأدوار المهنية ذات الصلة.',
      'edit': 'تعديل',
      'deleteExperienceTitle': 'حذف الخبرة؟',
      'deleteExperienceMessage': 'هل تريد حذف «{jobTitle}» في «{company}»؟',
      '@deleteExperienceMessage': {
        'placeholders': {
          'jobTitle': {'type': 'String'},
          'company': {'type': 'String'},
        },
      },
      'experienceDeleted': 'تم حذف الخبرة بنجاح.',
      'newExperienceTitle': 'أضف خبرة عملية.',
      'editExperienceTitle': 'تعديل الخبرة العملية.',
      'experienceEditorSubtitle':
          'اجعل المعلومات دقيقة وواضحة وركز على مسؤولياتك وإنجازاتك الحقيقية.',
      'jobTitleLabel': 'المسمى الوظيفي',
      'jobTitleHint': 'مثال: مطور Flutter',
      'companyNameLabel': 'الشركة أو جهة العمل',
      'companyNameHint': 'مثال: AST Technologies',
      'locationLabel': 'الموقع',
      'locationHint': 'مثال: صنعاء، اليمن',
      'startDateLabel': 'تاريخ البداية',
      'endDateLabel': 'تاريخ النهاية',
      'selectDate': 'اختر التاريخ',
      'dateNotSpecified': 'غير محدد',
      'present': 'حتى الآن',
      'currentlyWorkingHere': 'ما زلت أعمل هنا',
      'currentlyWorkingHereSubtitle': 'لن يتم حفظ تاريخ انتهاء لهذه الوظيفة.',
      'currentPosition': 'الوظيفة الحالية',
      'experienceDescriptionLabel': 'المسؤوليات والإنجازات',
      'experienceDescriptionHint':
          'اكتب مسؤولياتك الفعلية والنتائج والإنجازات القابلة للقياس...',
      'createExperience': 'إضافة الخبرة',
      'experienceCreated': 'تمت إضافة الخبرة بنجاح.',
      'experienceUpdated': 'تم تحديث الخبرة بنجاح.',
      'companyNameRequired': 'اسم الشركة أو جهة العمل مطلوب.',
      'jobTitleRequired': 'المسمى الوظيفي مطلوب.',
      'maximum150Characters': 'الحد الأقصى 150 حرفًا.',
      'maximum200Characters': 'الحد الأقصى 200 حرف.',
      'endDateBeforeStart': 'لا يمكن أن يكون تاريخ النهاية قبل تاريخ البداية.',
      'experienceTruthNotice':
          'استخدم مسؤولياتك وإنجازاتك الحقيقية فقط. لن يختلق AST-CV خبرة عملية غير موجودة.',
    },

    'fr': {
      'experienceEyebrow': 'EXPÉRIENCE PROFESSIONNELLE',
      'experienceScreenTitle': 'Votre expérience.',
      'experienceScreenSubtitle':
          'Ajoutez les postes qui représentent réellement votre parcours professionnel et vos réalisations.',
      'addExperience': 'Ajouter une expérience',
      'noExperienceTitle': 'Aucune expérience ajoutée',
      'noExperienceSubtitle':
          'Ajoutez votre parcours professionnel réel, vos stages ou vos fonctions pertinentes.',
      'edit': 'Modifier',
      'deleteExperienceTitle': 'Supprimer cette expérience ?',
      'deleteExperienceMessage':
          'Supprimer « {jobTitle} » chez « {company} » ?',
      '@deleteExperienceMessage': {
        'placeholders': {
          'jobTitle': {'type': 'String'},
          'company': {'type': 'String'},
        },
      },
      'experienceDeleted': 'Expérience supprimée avec succès.',
      'newExperienceTitle': 'Ajouter une expérience.',
      'editExperienceTitle': 'Modifier l’expérience.',
      'experienceEditorSubtitle':
          'Gardez les informations précises et concentrez-vous sur vos responsabilités et réalisations réelles.',
      'jobTitleLabel': 'Intitulé du poste',
      'jobTitleHint': 'Ex. Développeur Flutter',
      'companyNameLabel': 'Entreprise',
      'companyNameHint': 'Ex. AST Technologies',
      'locationLabel': 'Lieu',
      'locationHint': 'Ex. Sanaa, Yémen',
      'startDateLabel': 'Date de début',
      'endDateLabel': 'Date de fin',
      'selectDate': 'Choisir une date',
      'dateNotSpecified': 'Non précisée',
      'present': 'Aujourd’hui',
      'currentlyWorkingHere': 'Je travaille actuellement ici',
      'currentlyWorkingHereSubtitle':
          'Aucune date de fin ne sera enregistrée pour ce poste.',
      'currentPosition': 'POSTE ACTUEL',
      'experienceDescriptionLabel': 'Responsabilités et réalisations',
      'experienceDescriptionHint':
          'Décrivez vos responsabilités réelles, vos résultats et vos réalisations mesurables...',
      'createExperience': 'Ajouter l’expérience',
      'experienceCreated': 'Expérience ajoutée avec succès.',
      'experienceUpdated': 'Expérience mise à jour avec succès.',
      'companyNameRequired': 'Le nom de l’entreprise est obligatoire.',
      'jobTitleRequired': 'L’intitulé du poste est obligatoire.',
      'maximum150Characters': '150 caractères maximum.',
      'maximum200Characters': '200 caractères maximum.',
      'endDateBeforeStart':
          'La date de fin ne peut pas précéder la date de début.',
      'experienceTruthNotice':
          'Utilisez uniquement vos responsabilités et réalisations réelles. AST-CV n’inventera jamais d’expérience professionnelle.',
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
