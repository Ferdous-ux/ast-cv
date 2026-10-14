import 'package:flutter/material.dart';

import '../localization/l10n_extension.dart';
import '../theme/app_colors.dart';

class AppLogo extends StatelessWidget {
  final double size;
  final bool showName;

  const AppLogo({super.key, this.size = 48, this.showName = true});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: size,
          height: size,
          decoration: BoxDecoration(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(size * 0.27),
            boxShadow: [
              BoxShadow(
                color: AppColors.primary.withValues(alpha: 0.22),
                blurRadius: 22,
                offset: const Offset(0, 9),
              ),
            ],
          ),
          alignment: Alignment.center,
          child: Text(
            'A',
            style: TextStyle(
              color: Colors.white,
              fontSize: size * 0.46,
              height: 1,
              fontWeight: FontWeight.w800,
              letterSpacing: -1,
            ),
          ),
        ),
        if (showName) ...[
          const SizedBox(width: 12),
          Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'AST-CV',
                style: TextStyle(
                  fontSize: 20,
                  height: 1.05,
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.6,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                context.l10n.careerIntelligence,
                style: const TextStyle(
                  fontSize: 7.5,
                  height: 1,
                  fontWeight: FontWeight.w700,
                  letterSpacing: 1.05,
                  color: AppColors.textMuted,
                ),
              ),
            ],
          ),
        ],
      ],
    );
  }
}
