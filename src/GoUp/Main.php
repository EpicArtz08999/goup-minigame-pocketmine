<?php

namespace GoUp;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\event\Listener;
use pocketmine\scheduler\CallbackTask;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
class Main extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener
{
    public $test = array();
    public $players = array();
    public $open_arenas = 1;
    public $time = 33;
    public $start_state = false;
    public $over_time = 13;
    public $over_state = false;
    public $game = false;
    public $items = array(270 => "§6Pickage", 271 => "§6Axe", 269 => "§6Shovel");
    public $start_count = 0;
    public function onEnable()
    {
        $c = new \GoUp\Config($this->getDataFolder() . "/arena.yml", \GoUp\Config::YAML);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new \pocketmine\scheduler\CallbackTask(array($this, "timer")), 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new \pocketmine\scheduler\CallbackTask(array($this, "overTimer")), 20);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getDefaultLevel()->setAutoSave(\false);
    }
    public function onInteract(\pocketmine\event\player\PlayerInteractEvent $e)
    {
        $p = $e->getPlayer();
        $i = $e->getItem();
        switch ($i->getId()) {
            case 345:
                $this->joinGame($p);
                break;
        }
    }
    public function start($p)
    {
        $p->getInventory()->clearAll();
        $i = \pocketmine\item\Item::get(345, 0, 1);
        $i->setCustomName("§r§6Join to game");
        $p->getInventory()->addItem($i);
    }
    public function joinServer(\pocketmine\event\player\PlayerJoinEvent $e)
    {
        $this->start($e->getPlayer());
        $e->getPlayer()->teleport(new \pocketmine\math\Vector3($this->getServer()->getDefaultLevel()->getSafeSpawn()->getX(), $this->getServer()->getDefaultLevel()->getSafeSpawn()->getY(), $this->getServer()->getDefaultLevel()->getSafeSpawn()->getZ()));
    }
    public function onRespawn(\pocketmine\event\player\PlayerRespawnEvent $e)
    {
        $c = new \GoUp\Config($this->getDataFolder() . "/arena.yml", \GoUp\Config::YAML);
        if (isset($this->test[$e->getPlayer()->getName()])) {
            $e->getPlayer()->setGamemode(3);
            $e->getPlayer()->teleport($c->get('x'), $c->get(y), $c->get(z));
            return;
        }
        $e->getPlayer()->setGamemode(0);
    }
    public function joinGame(\pocketmine\Player $p)
    {
        if (!$this->game) {
            if (!isset($this->players[$p->getName()])) {
                $this->players[$p->getName()] = $p;
                $c = count($this->players);
                if ($c < 16) {
                    if ($c == 1) {
                        $this->start_state = true;
                    } else {
                        $this->start_state = true;
                    }
                    foreach ($this->players as $n => $p2) {
                        $p2->sendMessage("§a" . $p->getName() . " §6join to arena! §8(§a" . $c . "§7/§a16§8)");
                        $p2->getInventory()->clearAll();
                    }
                } elseif ($c == 16) {
                    foreach ($this->players as $n => $p2) {
                        $p2->sendMessage("§a" . $p->getName() . " §6join to arena! §8(§a" . $c . "§7/§a16§8)");
                    }
                    $this->time = 20;
                } else {
                    $p->sendMessage("§6Arena is full!");
                }
            }
        } else {
            $p->sendMessage("§6Game is starting!");
        }
    }
    public function overTimer()
    {
        if ($this->over_state) {
            $this->over_time--;
            switch ($this->over_time) {
                case 300:
                    $msg = "§6Pritection will disable in §a5§6 minuts!";
                    break;
                case 240:
                    $msg = "§6Pritection will disable in §a4§6 minuts!";
                    break;
                case 180:
                    $msg = "§6Pritection will disable in §a3§6 minuts!";
                    break;
                case 120:
                    $msg = "§6Pritection will disable in §a2§6 minuts!";
                    break;
                case 60:
                    $msg = "§6Pritection will disable in §a1§6 minute!";
                    break;
                case 30:
                    $msg = "§6Pritection will disable in §a30§6 seconds!";
                    break;
                case 10:
                    $msg = "§6Pritection will disable in §a10§6 seconds!";
                    break;
                case 5:
                    $msg = "§6Pritection will disable in §a5§6 seconds!";
                    break;
                case 4:
                    $msg = "§6Pritection will disable in §a4§6 seconds!";
                    break;
                case 3:
                    $msg = "§6Pritection will disable in §a3§6 seconds!";
                    break;
                case 2:
                    $msg = "§6Pritection will disable in §a2§6 seconds!";
                    break;
                case 1:
                    $msg = "§6Pritection will disable in §a1§6 second!";
                    break;
                case 0:
                    $this->game = true;
                    $this->over_state = false;
                    $this->over_time = 300;
                    $msg = "§6Pritection is dasable! " . $p->getLevel()->getName() . "";
                    break;
            }
            foreach ($this->players as $n => $p) {
                $p->sendMessage($msg);
            }
        }
    }
    public function timer()
    {
        $c = new \GoUp\Config($this->getDataFolder() . "/arena.yml", \GoUp\Config::YAML);
        if ($this->start_state) {
            $this->time--;
            switch ($this->time) {
                case 120:
                    $msg = "§6Start in §a2§6 minuts!";
                case 90:
                    $msg = "§6Start in §a1.5§6 minuts!";
                    break;
                case 60:
                    $msg = "§6Start in §a1§6 minuts!";
                    break;
                case 30:
                    $msg = "§6Start in §a30 §6seconds!";
                    break;
                case 10:
                    $msg = "§6Start in §a10 §6seconds!";
                    break;
                case 5:
                    $msg = "§6Start in §a5§6...";
                    break;
                case 4:
                    $msg = "§6Start in §a4§6...";
                    break;
                case 3:
                    $msg = "§6Start in §a3§6...";
                    break;
                case 2:
                    $msg = "§6Start in §a2§6...";
                    break;
                case 1:
                    $msg = "§6Start in §a1§6...";
                    break;
                case 0:
                    $this->start_state = false;
                    $this->over_state = true;
                    $this->overTimer();
                    $this->open_arenas = 0;
                    $this->start_time = 120;
                    $msg = "§6Game starting!";
                    $this->start_count = count($this->players);
                    $ci = 0;
                    foreach ($this->players as $n => $p) {
                        foreach ($this->items as $id => $name) {
                            $p->getInventory()->addItem(\pocketmine\item\Item::get($id, 0, 1)->setCustomName("§r" . $name));
                        }
                        $lvl2 = $this->getServer()->getLevelByName($c->get('world'));
                        switch ($ci) {
                            case 0:
                                $pos = new \pocketmine\level\Position($c->get('x'), $c->get(y), $c->get(z), $lvl2);
                                break;
                            case 1:
                                $pos = new \pocketmine\level\Position($c->get('x') + 1, $c->get(y), $c->get(z) + 1, $lvl2);
                                break;
                            case 2:
                                $pos = new \pocketmine\level\Position($c->get('x') + 2, $c->get(y), $c->get(z) + 2, $lvl2);
                                break;
                            case 3:
                                $pos = new \pocketmine\level\Position($c->get('x') + 3, $c->get(y), $c->get(z) + 3, $lvl2);
                                break;
                                //next time!
                        }
                        $ci++;
                        //$pos = new Vector3(100, 10, 100);
                        $p->teleport($pos);
                    }
                    break;
            }
            foreach ($this->players as $n => $p) {
                $p->sendMessage($msg);
            }
        }
    }
    public function onMove(\pocketmine\event\player\PlayerMoveEvent $e)
    {
        $p = $e->getPlayer();
        if (true) {
            $l = $p->getLevel()->getSafeSpawn();
            if ($p->y > $c->get('WorldRadius')) {
                $e->setCancelled();
                $p->sendMessage("§6World is small. :)");
            }
        }
    }
    public function onBreak(\pocketmine\event\block\BlockBreakEvent $e)
    {
        $p = $e->getPlayer();
        if (!isset($this->players[$p->getName()])) {
            $e->setCancelled();
        }
        if (!$this->game) {
            if ($e->getBlock()->getId() == 20) {
                $e->setCancelled();
            }
        }
    }
    public function onDamage(\pocketmine\event\entity\EntityDamageEvent $e)
    {
        if (!isset($this->players[$e->getEntity()->getPlayer()->getName()])) {
            $e->setCancelled();
        }
    }
    public function onQuit(\pocketmine\event\player\PlayerQuitEvent $e)
    {
        if (isset($this->players[$e->getPlayer()->getName()])) {
            $e->setQuitMessage(null);
            $p = $e->getPlayer();
            unset($this->players[$p->getName()]);
            foreach ($this->players as $n => $p) {
                $p->sendMessage("§a" . $p->getName() . "§6leave of game! §8(§6" . count($this->players) . "§7/§6" . $this->start_count . "§8)");
            }
            if (count($this->players) == 0 && $this->game == true) {
                $this->getServer()->shutdown();
            }
            if (count($this->players) == 1) {
                if ($this->game) {
                    $p = "";
                    $n = "";
                    foreach ($this->players as $nn => $pn) {
                        $p = $pn;
                        $n = $nn;
                    }
                    $this->getServer()->broadcastMessage("§8(§6GoUp§8)§6 Player §a" . $n . " is win§6!");
                    $ss = $this->getServer()->getDefaultLevel()->getSafeSpawn();
                    $l = $this->getServer()->getDefaultLevel();
                    unset($this->players[$n]);
                    $lvl = $p->getLevel();
                    $p->teleport(new \pocketmine\level\Position($ss->getX(), $ss->getY(), $ss->getZ(), $this->getServer()->getDefaultLevel()));
                    $lvl->unload();
                    $this->open_arenas = 1;
                    $this->game = false;
                    foreach ($this->getServer()->getOnlinePlayers() as $ps) {
                        $ps->close("", "§6Player §a" . $name . " is win§6!\n§6Now you can join to game!");
                    }
                    $this->getServer()->shutdown();
                    $this->start($p);
                }
            }
        }
    }
    public function onDeath(\pocketmine\event\player\PlayerDeathEvent $e)
    {
        $p2 = $e->getPlayer();
        $e->setDeathMessage(null);
        $p2->teleport(new \pocketmine\math\Vector3($this->getServer()->getDefaultLevel()->getSafeSpawn()->getX(), $this->getServer()->getDefaultLevel()->getSafeSpawn()->getY(), $this->getServer()->getDefaultLevel()->getSafeSpawn()->getZ()));
        $this->start($p2);
        $this->test[$p2->getName()] = true;
        unset($this->players[$p2->getName()]);
        foreach ($this->players as $n => $p) {
            $p->sendMessage("§a" . $p2->getName() . " §6death! §8(§6" . count($this->players) . "§7/§6" . $this->start_count . "§8)");
        }
        $p2->setGamemode(3);
        if (count($this->players) == 0) {
            $this->getServer()->shutdown();
        }
        if (count($this->players) == 1) {
            $name = "";
            $pp = "";
            foreach ($this->players as $n => $p) {
                $name = $n;
                $pp = $p;
            }
            $p = $pp;
            $this->getServer()->broadcastMessage("§8(§6GoUp§8)§6 Player §a" . $name . " is win§6!");
            $ss = $this->getServer()->getDefaultLevel()->getSafeSpawn();
            $l = $this->getServer()->getDefaultLevel();
            unset($this->players[$name]);
            $p->teleport(new \pocketmine\level\Position($ss->getX(), $ss->getY(), $ss->getZ(), $this->getServer()->getDefaultLevel()));
            $p->getLevel()->unload();
            $this->open_arenas = 1;
            $this->game = false;
            foreach ($this->getServer()->getOnlinePlayers() as $p) {
                $p->close("", "§6Player §a" . $name . " win§6!\n§6Now you can join to game!");
            }
            $this->getServer()->shutdown();
            $this->start($p);
        }
    }
}
