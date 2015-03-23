# build-tools-lib

This libraries provides features for [build-tools](https://github.com/javihgil/build-tools) package.

## Usage in other projects

**Include as git submodule**

```bash
    $ git submodule add git@github.com:javihgil/build-tools-libs.git lib
```
    
**Usage in phing**

```xml
    <!-- types -->
    <typedef name="modules" classname="lib.Phing.types.Modules" />
    <typedef name="module"  classname="lib.Phing.types.Module" />

    <!-- tasks -->
    <taskdef name="call"    classname="lib.Phing.tasks.CallTask" />
    <taskdef name="ccopy"   classname="lib.Phing.tasks.CodeCopyTask" />
    <taskdef name="composr" classname="lib.Phing.tasks.ComposrTask" />
    <taskdef name="dev"     classname="lib.Phing.tasks.DevTask" />
    <taskdef name="info"    classname="lib.Phing.tasks.InfoTask" />
    <taskdef name="modulei" classname="lib.Phing.tasks.ModuleIteratorTask" />
    <taskdef name="package" classname="lib.Phing.tasks.PackageTask" />
    <taskdef name="rm"      classname="lib.Phing.tasks.RmTask" />
    <taskdef name="repo"    classname="lib.Phing.tasks.RepositoryTask" />
    <taskdef name="symfony" classname="lib.Phing.tasks.SymfonyTask" />
    <taskdef name="test"    classname="lib.Phing.tasks.TestTask" />
```

## Types

### Module types

**Example**

```xml
    <modules id="modules">
        <module name="module1"/>
        <module name="module2"/>
    </modules>
```

**Usage**

```xml
    <modulei action="phing" modulesrefid="modules" task="target-name">
        <arg ... />
    </modulei>
```

## Tasks

### Call task

Executes a phing target. Extends original [phingcall](http://www.phing.info/docs/guide/stable/apbs25.html) 
task, except that it's execution is optional when not *required*.

**Example**

```xml
    <call target="target-name" required="true"/>
```

**Parameters**

- target: (string) name of target to execute.
- required: (bool, default false) if target definition is required.
- inheritAll: (bool, default: true)
- inheritRefs: (bool, default: false)

### Code Copy task

Copies code from source directory to target directory. Internally uses rsync command, so it's quicker 
than phing's [copy](http://www.phing.info/docs/guide/stable/apbs09.html) task. 

**Example**

```xml
    <ccopy from="src" to="target/build" stats="true" logFile="target/build.copy.log" excludes="build.xml"/>
```

**Parameters**

- from: (string) source directory
- to: (string) target directory
- logFile: (string) log file to store the command result
- excludes: (string) exclude files and directories.
- stats: (bool, default false) show process stats at the end of execution.

### Composer task

#### Get action

Gets *composer.phar* file. If already exits, executes self-update.

**Example**

```xml
    <composr action="get" />
```

**Parameters**

- pharFile: (string)

#### Self Update action

**Example**

```xml
    <composr action="self-update" />
```

**Parameters**

- pharFile: (string)

#### Install action

**Example**

```xml
    <composr action="install" dev="true" />
```

**Parameters**

- pharFile: (string, default 'composer.phar')
- dir: (string, default '.')
- dev: (bool, default false)
- optimizeautoloader: (bool, default false)
- preferDist: (bool, default true)
- noProgress: (bool, default true)
- profile: (bool, default true)
- verbosity: (string, default 'vv')

#### Check Release Dependencies action

**Example**

```xml
    <composr action="check-release-dependencies" />
```

**Parameters**

- jsonFile: (string)

#### Check Lock Updated action

**Example**

```xml
    <composr action="check-lock-updated" />
```

**Parameters**

- jsonFile: (string)
- lockFile: (string)

#### Check Development Version action

**Example**

```xml
    <composr action="check-dev-version"/>
```

**Parameters**

- jsonFile: (string)

#### Get Version action

**Example**

```xml
    <composr action="get-version" />
```

**Parameters**

- jsonFile (string)
- property: (string)
- release: (bool, default false)
- increment: (bool, default false)

#### Version Update action

**Example**

```xml
    <composr action="version-update" version="${release.version}" forceVersionType="release" />
```

**Parameters**

- jsonFile: (string)
- version: (string)
- forceVersionType: (string) dev|release

#### Dependency Update action

**Example**

```xml
    <composr action="dependency-update" />
```

**Parameters**

- jsonFile: (string)
- version: (string)
- dependencyGroup: (string)
- dependencyPackage: (string)
- dependencyPattern: (string)

#### Export action

**Example**

```xml
    <composr action="export" value="version" property="composer.version" />
```

**Parameters**

- jsonFile: (string)
- value: (string) name|version|type
- property: (string) property name to export into

### Dev task

#### Create Symlink action

**Example**

```xml
    <dev action="create-symlink" localDir="~/javihgil/test-bundle" vendorDir="vendor/javihgil/test-bundle" />
```

**Parameters**

- action: *create-symlink*
- localDir: (string) symlink target path
- vendorDir: (string) symlink location path

#### Remove Symlink action

**Example**

```xml
    <dev action="remove-symlink" vendorDir="vendor/javihgil/test-bundle" />
```

**Parameters**

- action: *remove-symlink*
- vendorDir: (string) symlink location path

### Info task

Shows some information in phing log.

**Show property example**

```xml
    <info show="property" property="composer.name"/>
```

**Show target example**

```xml
    <info show="target"/>
```

**Parameters**

- show: (string) property|target
- property: (string) name of the property to show

### Module Iterator task

*TODO*

### Package task

Creates a code package.

**Example**

```xml
    <package format="zip" dir="target/build" file="build.zip" log="build.zip.log" sha1="true" />
```

**Parameters**

- format: (string) zip|tgz
- dir: (string) source directory
- file: (string) package file
- log: (string) log file
- sha1: (bool, default false) create package sha1 file or not
- md5: (bool, default false) create package md5 file or not

### Repository task

Repository task allows to manage a private repository for composer packages. It's possible to download 
custom dependencies and stores build and release packages.

**Configuration**

```xml
    <property name="private.repository" value="true"/>
    <property name="private.repository.driver" value="s3"/>
    <property name="private.dependencies.regex" value="^javihgil\/"/>
```

**S3 Repository configuration**
    
```xml
    <property name="s3.build.bucket.path" value="bucket.name/builds"/>
    <property name="s3.release.bucket.path" value="bucket.name/releases"/>
    <property name="s3.cmd.bin" value="/usr/bin/s3cmd"/>
    <property name="s3.cmd.config" value="~/.s3cnf"/>
```

#### Download Dependencies action

**Example**

```xml
    <repo action="download-deps" driver="s3" packageRegex="^mycompany\/" />
```

**Parameters**

- driver
- packageRegex
- jsonFile
- localRepositoryDir

#### Check Update action

**Example**

```xml
    <repo action="check-update" driver="s3" />
```

**Parameters**

- driver
- lockFile

#### Upload Build action

**Example**

```xml
    <repo action="upload-build" file="${targetPath}/build.zip" sha1="true" driver="s3" />
```

**Parameters**

- jsonFile
- driver
- file
- sha1

#### Upload Release action

**Example**

```xml
    <repo action="upload-release" file="${targetPath}/release.zip" sha1="true" driver="s3" />
```

**Parameters**

- jsonFile
- driver
- file
- sha1

#### Download action

*TODO*

### Rm task

Removes a file, directory or package, using the *rm* linux command.

**Example**

```xml
    <rm file="target/build/composer.lock"/>
    <rm dir="target/build/target"/>
    <rm pattern="${targetPath}/build*" />
```

**Parameters**

- dir: (string) directory to remove
- file: (string) file to remove
- pattern: (string) expression to remove files or directories

### Symfony task

*TODO*

### Test task

*TODO*
