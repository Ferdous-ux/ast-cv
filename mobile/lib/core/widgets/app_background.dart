import 'package:flutter/material.dart';

import '../theme/app_colors.dart';

class AppBackground extends StatelessWidget {
  final Widget child;
  final bool showGlow;

  const AppBackground({super.key, required this.child, this.showGlow = true});

  @override
  Widget build(BuildContext context) {
    return DecoratedBox(
      decoration: const BoxDecoration(color: AppColors.background),
      child: Stack(
        children: [
          if (showGlow) ...[
            Positioned(
              top: -140,
              right: -120,
              child: _GlowCircle(
                size: 330,
                color: AppColors.primary.withValues(alpha: 0.10),
              ),
            ),
            Positioned(
              top: 160,
              left: -170,
              child: _GlowCircle(
                size: 300,
                color: AppColors.cyan.withValues(alpha: 0.08),
              ),
            ),
          ],
          child,
        ],
      ),
    );
  }
}

class _GlowCircle extends StatelessWidget {
  final double size;
  final Color color;

  const _GlowCircle({required this.size, required this.color});

  @override
  Widget build(BuildContext context) {
    return IgnorePointer(
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: RadialGradient(colors: [color, color.withValues(alpha: 0)]),
        ),
      ),
    );
  }
}
