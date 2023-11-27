<?php
namespace Config;

use Phpml\Classification\KNearestNeighbors;
use Phpml\Clustering\KMeans;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Preprocessing\Imputer;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\ModelManager;

class MachineLearning
{
    private $knnClassifier;
    private $kmeansClustering;
    private $vectorizer;
    private $imputer;

    public function __construct()
    {
        $this->knnClassifier = new KNearestNeighbors();
        $this->kmeansClustering = new KMeans(3);
        $this->vectorizer = new TokenCountVectorizer();
        $this->imputer = new Imputer();
    }

    public function trainKNNClassifier($samples, $labels)
    {
        // Vetoriza os dados de entrada
        $vectorizedData = $this->vectorizer->fit($samples)->transform($samples);

        // Completa os valores ausentes
        $imputedData = $this->imputer->fit($vectorizedData)->transform($vectorizedData);

        // Divide o conjunto de dados em treinamento e teste
        $dataset = new ArrayDataset($imputedData, $labels);
        $split = new RandomSplit($dataset, 0.3);

        // Treina o classificador KNN
        $this->knnClassifier->train($split->getTrainSamples(), $split->getTrainLabels());
    }

    public function predictWithKNN($sample)
    {
        // Vetoriza o sample de entrada
        $vectorizedSample = $this->vectorizer->transform([$sample]);

        // Realiza a previsão
        $prediction = $this->knnClassifier->predict($vectorizedSample);

        return $prediction[0];
    }

    public function trainKMeansClustering($data)
    {
        // Realiza o agrupamento dos dados
        $this->kmeansClustering->cluster($data);
    }

    public function getClusterLabels()
    {
        return $this->kmeansClustering->getClusterLabels();
    }

    public function saveModel($filePath)
    {
        // Salva o modelo treinado
        $modelManager = new ModelManager();
        $modelManager->saveToFile($this->knnClassifier, $filePath);
    }

    public function loadModel($filePath)
    {
        // Carrega um modelo treinado
        $modelManager = new ModelManager();
        $this->knnClassifier = $modelManager->restoreFromFile($filePath);
    }
}