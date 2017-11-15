<?php

namespace VMB\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use VMB\ResourceBundle\Entity\Resource;
use VMB\QuizBundle\Entity\Quiz;
use VMB\QuizBundle\Entity\MultiChoice;
use VMB\QuizBundle\Entity\SingleChoice;
use VMB\QuizBundle\Entity\TextArea;
use VMB\QuizBundle\Entity\NumericalValue;
use VMB\QuizBundle\Form\QuizType;
use VMB\QuizBundle\Form\listQuestType;

class QuizController extends Controller
{
    
    public function loadQuestionsAction(Request $request)
    {
        $session = $request->getSession();
        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        if($session->get('idQuests') == null and $id != null)
        {
            $usedResource = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:UsedResource')->find($id);
            $quiz = $usedResource->getResource()->getQuiz();
            $quests = array();
            foreach($quiz->getMultichoices() as $multichoice)
            {
                $quests[] = $multichoice;
            }
            foreach($quiz->getSinglechoices() as $singlechoice)
            {
                $quests[] = $singlechoice;
            }
            foreach($quiz->getTextareas() as $textarea)
            {
                $quests[] = $textarea;
            }
            foreach($quiz->getNumericalvalues() as $numericalvalue)
            {
                $quests[] = $numericalvalue;
            }
            $idQuests = array();
            foreach($quests as $quest)
            {
                $idQuests[] = $quest->getId();
            }
            if(count($idQuests)==0)
            {
                return $this->render('VMBQuizBundle:Quiz:noQuiz.html.twig');
            }
            shuffle($idQuests);
            $idQuests = array_slice($idQuests, 0, 10);
            $quests = $this->getDoctrine()->getManager()->getRepository('VMBQuizBundle:Question')->findQuestionsByIds($idQuests);
            $form = $this
            ->get('form.factory')
            ->create(new listQuestType($quests));
            $session->set('idQuests',$idQuests);
            return $this->render('VMBQuizBundle:Quiz:questions.html.twig',array('form'=>$form->createView()));
        }
        elseif($request->isMethod('GET') and $id == null)
        {
            $hints = $this->getDoctrine()->getManager()->getRepository('VMBQuizBundle:Question')->findHintsByIds($session->get('idQuests'));
            return $this->render('VMBQuizBundle:Quiz:hints.html.twig',array('hints'=>$hints));
        }
        elseif($id == null)
        {
            $answers = $request->request->get("vmb_quizbundle_questions");
            $questions = $this->getDoctrine()->getManager()->getRepository('VMBQuizBundle:Question')->findQuestionsByIds($session->get('idQuests'));
            $correction = $this->correctAnswers($questions,$answers);
            return $this->render('VMBQuizBundle:Quiz:correction.html.twig',array('correction'=>$correction));
        }
    }
    
    private function correctAnswers($questions, $answers)
    {
        $correction = array();
        $stringSol = '';
        $solution = $this->transformSolutionToArray($questions);
        $i=0;
        foreach($questions as $quest)
        {
            $stringSol = '';
            if(array_key_exists('question_'.$i ,$answers))
            {
                $answer = $answers['question_'.$i];
                if($quest instanceof MultiChoice)
                {
                    $listCorrectProps = $solution['question_'.$i];
                    $stringSol = '<ul>';
                    foreach($solution['question_'.$i] as $correctProp)
                    {                        
                        if(in_array($correctProp,$answer))
                        {
                            $key = array_search($correctProp, $listCorrectProps) ;
                            unset($listCorrectProps[$key]);
                            unset($answer[$key]);
                        }
                        $stringSol = $stringSol.'<li>'.$correctProp.'</li>';
                    }
                    $stringSol = $stringSol.'</ul>';
                    if(count($listCorrectProps) == 0 && count($answer) == 0)
                    {
                        if(count($solution['question_'.$i])>1)
                            $correction[] = '<div class="alert alert-success">Correct : Les réponses sont bien :<br/>'.$stringSol.'</div>' ; 
                        else
                            $correction[] = '<div class="alert alert-success">Correct : La réponse est bien :<br/>'.$stringSol.'</div>' ;  
                    }
                    else
                    {
                        if(count($solution['question_'.$i])>1)
                        {
                            $correction[] = '<div class="alert alert-danger">Faux : Les réponses sont :<br/>'.$stringSol.'</div>' ; 
                        }         
                        else
                        {
                            $correction[] = '<div class="alert alert-danger">Faux : La réponse est :<br/>'.$stringSol.'</div>' ; 
                        }       
                    }
                }
                elseif($quest instanceof SingleChoice)
                {
                    $boolean = false;
                    $listCorrectProps = $solution['question_'.$i];
                    $stringSol = '<ul>';
                    foreach($solution['question_'.$i] as $correctProp)
                    {
                        if(strcasecmp(trim($correctProp),trim($answer))==0)
                        {
                            $boolean = true;
                        }
                        $stringSol = $stringSol.'<li>'.$correctProp.'</li>';
                    }
                    $stringSol = $stringSol.'</ul>';
                    if($boolean)
                    {
                        if(count($solution['question_'.$i])>1)
                        {
                            $correction[] = '<div class="alert alert-success">Correct : Les réponses sont bien :<br/>'.$stringSol.'</div>' ; 
                        }         
                        else
                        {
                            $correction[] = '<div class="alert alert-success">Correct : La réponse est bien :<br/>'.$stringSol.'</div>' ; 
                        }
                    }
                    else
                    {
                        if(count($solution['question_'.$i])>1)
                        {
                            $correction[] = '<div class="alert alert-danger">Faux : Les réponses sont :<br/>'.$stringSol.'</div>' ; 
                        }
                        else
                        {
                            $correction[] = '<div class="alert alert-danger">Faux : La réponse correcte est :<br/>'.$stringSol.'</div>' ; 
                        }       
                    }
                }
                elseif($quest instanceof TextArea)
                {
                    if(array_key_exists('question_'.$i ,$answers))
                    {
                        $answer = $answers['question_'.$i];
                        if(strcasecmp(trim($quest->getSolution()),trim($answer))==0)
                        {
                            $correction[] = '<div class="alert alert-success">Correct : La réponse est bien :<br/>'.$quest->getSolution().'</div>';
                        }
                        else
                        {
                            $correction[] = '<div class="alert alert-danger">Faux : La réponse est :<br>'.$quest->getSolution().'</div>';
                        }
                    }
                }
                elseif($quest instanceof NumericalValue)
                {
                    if(array_key_exists('question_'.$i ,$answers))
                    {
                        $answer = $answers['question_'.$i];
                        if(floatval($answer)>=floatval($quest->getValStart()) && floatval($answer)<=floatval($quest->getValEnd()))
                        {
                            $correction[] = '<div class="alert alert-success">Correct</div>';
                        }
                        else
                        {
                            $correction[] = '<div class="alert alert-danger">Faux</div>';
                        }
                    }
                }
            }
            else
            {
                if($quest instanceof MultiChoice)
                {
                    $listCorrectProps = $solution['question_'.$i];
                    $stringSol = '<ul>';
                    foreach($solution['question_'.$i] as $correctProp)
                    {
                        $stringSol = $stringSol.'<li>'.$correctProp.'</li>';
                    }
                    $stringSol = $stringSol.'</ul>';
                    if(count($solution['question_'.$i])>1)
                        $correction[] = '<div class="alert alert-danger">Faux : Les réponses sont :<br/>'.$stringSol.'</div>' ; 
                    else
                        $correction[] = '<div class="alert alert-danger">Faux : La réponse correcte est :<br/>'.$stringSol.'</div>' ;
                }
                elseif($quest instanceof SingleChoice)
                {
                    $listCorrectProps = $solution['question_'.$i];
                    $stringSol = '<ul>';
                    foreach($solution['question_'.$i] as $correctProp)
                    {
                        $stringSol = $stringSol.'<li>'.$correctProp.'</li>';
                    }
                    $stringSol = $stringSol.'</ul>';
                    if(count($solution['question_'.$i])>1)
                            $correction[] = '<div class="alert alert-danger">Faux : Les réponses sont :<br/>'.$stringSol.'</div>' ; 
                        else
                            $correction[] = '<div class="alert alert-danger">Faux : La réponse correcte est :<br/>'.$stringSol.'</div>' ; 
                }
                elseif($quest instanceof TextArea)
                {
                    if(array_key_exists('question_'.$i ,$answers))
                    {
                        $correction[] = '<div class="alert alert-danger">Faux : La réponse est :<br/>'.$quest->getSolution().'</div>';
                    }
                }
                elseif($quest instanceof NumericalValue)
                {
                    if(array_key_exists('question_'.$i ,$answers))
                    {
                        $correction[] = '<div class="alert alert-danger">Faux</div>';
                    }
                }
            }
            $i++;
            $this->getDoctrine()->getManager()->flush();
        }
        return $correction;
    }

    private function transformSolutionToArray($questions)
    {
        $solution = array();
        $i=0;
        foreach($questions as $quest)
        {
            if($quest instanceof MultiChoice)
            {
                $solution['question_'.$i] = array();
                $props = $quest->getPropositions();
                foreach($props as $prop)
                {
                    if($prop->getIsCorrect())
                    {
                        $solution['question_'.$i][] = $prop->getProposition();
                    }
                }
            }
            if($quest instanceof SingleChoice)
            {
                $solution['question_'.$i] = array();
                $props = $quest->getPropositions();
                foreach($props as $prop)
                {
                    if($prop->getIsCorrect())
                    {
                        $solution['question_'.$i][] = $prop->getProposition();
                    }
                }
            }
            elseif($quest instanceof TextArea)
            {
                $solution[] = $quest->getSolution();
            }
            elseif($quest instanceof NumericalValue)
            {
                $solution['question_'.$i] = array($quest->getValStart(), $quest->getValEnd());
            }
            $i++;
        }
        return $solution;
    }
}

