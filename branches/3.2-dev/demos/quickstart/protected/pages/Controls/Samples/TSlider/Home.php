<?php
class Home extends TPage 
{
	public function testsubmit ($sender, $param)
	{
		$this->hSliderResult->setText('submit : '.$this->hSlider->getValue());	
		$this->vSliderResult->setText('submit : '.$this->vSlider->getValue());
	}
	
	public function sliderChanged ($sender, $param)
	{
		$this->hSliderResult->setText('onSliderChanged : '.$sender->getValue());
	}
}
?>