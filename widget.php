<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Process_Timeline_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'process_timeline_custom'; }
    public function get_title()      { return esc_html__( 'Process Timeline', 'process-timeline-widget' ); }
    public function get_icon()       { return 'eicon-time-line'; }
    public function get_categories() { return [ 'general' ]; }

    // ── Styles ────────────────────────────────────────────────────────────
    private function render_styles() { ?>
        <style>
          .timeline-wrapper {
            --gold:         #C9A563;
            --node-size-lg: 76px;
            --line-h:       0.5px;
          }
          .timeline-wrapper {
            position: relative;
            width: calc(100% - 150px);
            margin: 0 auto;
          }
          .line-track {
            position: absolute;
            top: calc(var(--node-size-lg) / 2);
            left: calc(var(--node-size-lg) / 2);
            right: calc(var(--node-size-lg) / 2);
            height: var(--line-h);
          }
          .line-fill {
            position: absolute;
            inset: 0;
            background: #fff;
            transform-origin: left center;
            transform: scaleX(0);
          }
          .line-glow {
            position: absolute;
            top: 50%;
            right: -4px;
            transform: translateY(-50%);
            width: 8px; height: 8px;
            border-radius: 50%;
            opacity: 0;
          }
          .steps-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
          }
          .timeline-wrapper .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: var(--node-size-lg);
            opacity: 0;
            transform: translateY(16px);
          }
          .step-node {
            width: var(--node-size-lg); height: var(--node-size-lg);
            border-radius: 50%;
            border: 1px solid #C9A563;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: radial-gradient(circle at 40% 40%, #2A2A2A, #2A2A2A);
            transition: border-color 0.4s ease;
          }
          .step-node::after {
            content: '';
            position: absolute;
            inset: 4px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, rgba(201,169,110,0.06), transparent 60%);
          }
          .step-number {
            font-style: italic;
            font-weight: 300;
            font-size: 20px;
            color: var(--gold);
            letter-spacing: 0.05em;
            position: relative;
            z-index: 1;
          }
          .step.is-active .step-node { border-color: var(--gold); }
          .step-label {
            font-weight: 300;
            color: #fff;
            line-height: 1;
            margin: 20px 0 15px;
            display: block;
          }
          .step-sub {
            font-weight: 300;
            font-size: 0.75rem;
            color: #fff;
            text-align: center;
          }
          .step .step-text { text-align: center; min-width: 211px; }

          /* Vertical track (mobile) */
          .vertical-line-track {
            display: none;
            position: absolute;
            left: calc(80px + var(--node-size-lg) / 2 - 0.5px);
            top: calc(var(--node-size-lg) / 2);
            bottom: calc(var(--node-size-lg) / 2);
            width: var(--line-h);
          }
          .vertical-line-fill {
            position: absolute;
            left: 0; right: 0; top: 0;
            height: 0%;
            background: #fff;
            transform-origin: top center;
          }

          /* Mobile */
          @media (max-width: 768px) {
            .timeline-wrapper { width: 100%; max-width: 360px; }
            .line-track { display: none; }
            .vertical-line-track { display: block !important; }
            .steps-row {
              flex-direction: column;
              align-items: flex-start;
              gap: 0;
              padding-left: 80px;
            }
            .timeline-wrapper .step {
              flex-direction: row;
              align-items: center;
              width: 100%;
              gap: 24px;
              padding: 18px 0;
            }
            .step-node { flex-shrink: 0; }
            .step-text { display: flex; flex-direction: column; align-items: flex-start; }
            .step-label { margin-top: 0; text-align: left; white-space: normal; }
            .step-sub   { margin-top: 4px; text-align: left; }
            .step .step-text { min-width: auto; }
          }

          /* Tablet */
          @media (min-width: 769px) and (max-width: 1024px) {
            .step-label { font-size: 1rem; }
            .step-sub   { font-size: 0.65rem; }
            .step .step-text { min-width: auto; }
          }

          /* Always visible inside Elementor editor */
          .elementor-editor-active .timeline-wrapper .step {
            opacity: 1 !important;
            transform: none !important;
          }
        </style>
    <?php }

    // ── Controls ──────────────────────────────────────────────────────────
    protected function register_controls() {
        $this->start_controls_section( 'section_steps', [
            'label' => esc_html__( 'Steps', 'process-timeline-widget' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'step_label', [
            'label'       => esc_html__( 'Label', 'process-timeline-widget' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Step',
            'label_block' => true,
        ] );
        $repeater->add_control( 'step_sub', [
            'label'       => esc_html__( 'Sub-label', 'process-timeline-widget' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Description',
            'label_block' => true,
        ] );

        $this->add_control( 'steps', [
            'label'       => esc_html__( 'Steps', 'process-timeline-widget' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'step_label' => 'Brief',   'step_sub' => 'Understanding Your Vision' ],
                [ 'step_label' => 'Refine',  'step_sub' => 'Design & Development' ],
                [ 'step_label' => 'Craft',   'step_sub' => 'In-house Manufacture' ],
                [ 'step_label' => 'Install', 'step_sub' => 'Professional Fitting' ],
                [ 'step_label' => 'Perfect', 'step_sub' => 'Final Detailing' ],
            ],
            'title_field' => '{{{ step_label }}}',
        ] );

        $this->end_controls_section();
    }

    // ── Frontend render ───────────────────────────────────────────────────
    protected function render() {
        $settings = $this->get_settings_for_display();
        $steps    = $settings['steps'] ?? [];
        $this->render_styles();
        ?>
        <div class="timeline-wrapper">
          <div class="line-track">
            <div class="line-fill" id="lineFill">
              <div class="line-glow" id="lineGlow"></div>
            </div>
          </div>
          <div class="vertical-line-track" id="verticalTrack">
            <div class="vertical-line-fill" id="verticalFill"></div>
          </div>
          <div class="steps-row">
            <?php foreach ( $steps as $index => $step ) :
                $num = str_pad( $index + 1, 2, '0', STR_PAD_LEFT );
            ?>
            <div class="step" data-step="<?php echo esc_attr( $index ); ?>">
              <div class="step-node">
                <span class="step-number"><?php echo esc_html( $num ); ?></span>
              </div>
              <div class="step-text">
                <span class="step-label"><?php echo esc_html( $step['step_label'] ); ?></span>
                <span class="step-sub"><?php echo esc_html( $step['step_sub'] ); ?></span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php
    }

    // ── Editor (live preview) ─────────────────────────────────────────────
    protected function content_template() {
        $this->render_styles();
        ?>
        <div class="timeline-wrapper">
          <div class="line-track">
            <div class="line-fill" style="transform:scaleX(1);"></div>
          </div>
          <div class="vertical-line-track">
            <div class="vertical-line-fill" style="height:100%;"></div>
          </div>
          <div class="steps-row">
            <# _.each( settings.steps, function( step, i ) {
                var num = ( '0' + ( i + 1 ) ).slice(-2);
            #>
            <div class="step" style="opacity:1;transform:none;">
              <div class="step-node">
                <span class="step-number">{{ num }}</span>
              </div>
              <div class="step-text">
                <span class="step-label">{{ step.step_label }}</span>
                <span class="step-sub">{{ step.step_sub }}</span>
              </div>
            </div>
            <# } ); #>
          </div>
        </div>
        <?php
    }
}
