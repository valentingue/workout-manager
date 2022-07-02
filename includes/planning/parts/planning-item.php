<?php 
/* var_dump($entry['coach']); 
die; */
?>

<div class="fitplan-planning-item fitplan-planning-item-workout-<?php echo $entry['workout']['ID']; ?>" data-position-id="<?php echo $id ?>" style="top: <?php echo $entry['top']; ?>; height: <?php echo $entry['height']; ?>;">
  <div class="fitplan-planning-item-inside"
    style="
      <?php
        if($this->datas['fitplan_planning_workout_display_color'] == "on"):
          echo 'background-color: '.$this->datas['fitplan_planning_background_color'];
        else:
          echo 'background-color: '.$this->datas['fitplan_planning_workout_default_color'];
        endif;
      ?>;
      <?php echo 'color: '.$this->datas['fitplan_planning_workout_text_color']; ?>;
      <?php echo 'border-radius: '.$this->datas['fitplan_planning_workout_radius']; ?>px;"

    data-color="<?php
      if($this->datas['fitplan_planning_workout_display_color']):
        echo $this->datas['fitplan_planning_workout_default_color'];
      else:
        echo $entry['workout']['fitplan_planning_workout_display_color'];
      endif;
    ?>">

    <div class="fitplan-planning-item-pic" <?php if($this->datas['fitplan_planning_workout_display_pic'] == "off" or intval($entry['height']) < 50 /* or !$entry['workout']['collective_workout_field_pic']['isset'] */): ?>style="display: none;"<?php endif; ?>>
      <img src="<?php if(isset($entry['workout']['collective_workout_field_pic']['url'])): echo $entry['workout']['collective_workout_field_pic']['url']; endif; ?>" alt="<?php echo $entry['workout']['post_title']; ?>">
    </div>

    <p class="fitplan-planning-item-title" data-workout-id="<?php echo $entry['workout']['ID']; ?>" <?php if($this->datas['fitplan_planning_workout_display_title'] == "off" and intval($entry['height']) >= 50 /* and $entry['workout']['collective_workout_field_pic']['isset'] */): ?>style="display: none;"<?php endif; ?>>
      <?php echo $entry['workout']['post_title']; ?>
    </p>

    <p class="fitplan-planning-item-hour">
      <span class="fitplan-planning-item-hour-start" data-start="<?php echo $entry['start']; ?>"><?php echo $entry['start_display']; ?></span>
      -
      <span class="fitplan-planning-item-hour-finish" data-finish="<?php echo $entry['finish']; ?>"><?php echo $entry['finish_display']; ?></span>
    </p>
  </div>

  <div class="fitplan-planning-item-bubble">
    <?php if(isset($entry['workout']['collective_workout_field_pic'])): ?>
    <p class="fitplan-planning-modal-pic">
      <img src="<?php echo $entry['workout']['collective_workout_field_pic']['url']; ?>" alt="<?php echo $entry['workout']['post_title']; ?>">
    </p>
    <?php else: ?>
    <p class="fitplan-planning-modal-title">
      <?php echo $entry['workout']['post_title']; ?>
    </p>
    <?php endif; ?>

    <div class="fitplan-planning-modal-hour">
      <span class="fitplan-planning-modal-day"><?php echo $day['name']; ?></span>
      <span class="fitplan-planning-modal-hour-start"><?php echo $entry['start_display']; ?></span>
      -
      <span class="fitplan-planning-modal-hour-finish"><?php echo $entry['finish_display']; ?></span>
    </div>

    <div class="fitplan-planning-modal-desc">
      <?php echo wp_trim_words(nl2br($entry['workout']['collective_workout_field_desc']), 55); ?>
    </div>

    <?php if($entry['workout']['permalink'] != ""): ?>
    <p class="fitplan-planning-modal-link"><a target="_blank" href="<?php echo $entry['workout']['permalink']; ?>"><?php _e('Visit official page', 'fitness-schedule'); ?></a></p>
    <?php endif; ?>

    <?php if(isset($entry['coach'])): ?>
    <div class="fitplan-planning-modal-coach">
      <img class="fitplan-planning-modal-coach-img" src="<?php echo $entry['coach']['picture']; ?>" alt="<?php echo $entry['coach']['post']->post_title; ?>">
      <span class="fitplan-planning-modal-coach-by"><?php _e('By', 'fitness-schedule'); ?></span>
      <br>
      <strong class="fitplan-planning-modal-coach-name" data-coach-id="<?php echo $entry['coach']['post']->ID; ?>"><?php echo $entry['coach']['post']->post_title; ?></strong>
      <div class="fitplan-planning-modal-coach-bio">
        <?php echo $entry['coach']['acf']['coach_field_coach_desc']; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
